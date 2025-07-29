<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project;

use App\Controller\ErrorHandler;
use App\DTO\Work\Task\TaskMetaView;
use App\Infrastructure\Inertia\InertiaService;
use App\Mapper\Work\TaskMapper;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Task\Status;
use App\Model\Work\Entity\Projects\Task\Type;
use App\Model\Work\UseCase\Projects\Task\Create;
use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\ReadModel\Work\Projects\Task\Filter;
use App\ReadModel\Work\Projects\Task\TaskFetcher;
use App\ReadModel\Work\Projects\Task\TaskMetaProvider;
use App\Security\Voter\Work\Projects\ProjectAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/work/projects/{id}/tasks', name: 'work.projects.project.tasks')]
class TasksController extends AbstractController
{
    private const PER_PAGE = 10;

    private TaskFetcher $tasks;
    private ErrorHandler $errors;

    public function __construct(
        TaskFetcher $tasks,
        ErrorHandler $errors,
        private SerializerInterface $serializer)
    {
        $this->tasks = $tasks;
        $this->errors = $errors;
    }

    #[Route('', name: '', methods: ['GET'])]
    public function index(
        Project $project,
        Request $request,
        MemberFetcher $memberFetcher,
        InertiaService $inertia,
        TaskMetaProvider $taskMetaProvider,
    ): Response {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        $filter = Filter\Filter::all()->withProject($project->getId()->getValue());

        $filter = $filter
            ->withType($request->query->get('type'))
            ->withText($request->query->get('text'))
            ->withPriority($request->query->get('priority'))
            ->withStatus($request->query->get('status'))
            ->withExecutor($request->query->get('executor'))
            ->withAuthor($request->query->get('author'))
            ->withRoots($request->query->get('roots'));

        $pagination = $this->tasks->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort'),
            $request->query->get('direction')
        );

        $tasks = array_map(fn ($task) => TaskMapper::map($task), $pagination->getItems());
        $normalizedTasks = $this->serializer->normalize($tasks, null, ['groups' => ['task:list']]);

        $meta = new TaskMetaView(); // DTO
        $props = $taskMetaProvider->get();

        $meta->statuses = $props['statuses'];
        $meta->types = $props['types'];
        $meta->priorities = $props['priorities'];

        return $inertia->render($request, 'Work/Projects/Project/Tasks/Index', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'tasks' => $normalizedTasks,
            'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'lastPage' => ceil($pagination->getTotalItemCount() / self::PER_PAGE),
                'total' => $pagination->getTotalItemCount(),
            ],
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 't.id'),
            'direction' => $request->query->get('direction', 'asc'),
            'meta' => $this->serializer->normalize($meta, null, ['groups' => ['task:list']]),
        ]);
    }

    private function mapMembers(array $list): array
    {
        $result = [];

        foreach ($list as $item) {
            $result[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'group' => $item['group'],
            ];
        }

        return $result;
    }

    #[Route('/me', name: '.me', methods: ['GET'])]
    public function me(
        Project $project,
        Request $request,
        TaskFetcher $taskFetcher,
        MemberFetcher $memberFetcher,
        InertiaService $inertia,
        TaskMetaProvider $taskMetaProvider,
    ): Response {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        $filter = Filter\Filter::forProject($project->getId()->getValue());

        $pagination = $taskFetcher->all(
            $filter->withExecutor($this->getUser()->getId()),
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 't.id'),
            $request->query->get('direction', 'asc')
        );
        $tasks = array_map(fn ($task) => TaskMapper::map($task), $pagination->getItems());
        $normalizedTasks = $this->serializer->normalize($tasks, null, ['groups' => ['task:list']]);

        $meta = new TaskMetaView(); // DTO
        $props = $taskMetaProvider->get();

        $meta->statuses = $props['statuses'];
        $meta->types = $props['types'];
        $meta->priorities = $props['priorities'];

        return $inertia->render($request, 'Work/Projects/Project/Tasks/Index', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'tasks' => $normalizedTasks,
            'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'lastPage' => ceil($pagination->getTotalItemCount() / 50),
                'total' => $pagination->getTotalItemCount(),
            ],
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 't.date'),
            'direction' => $request->query->get('direction', 'asc'),
            'meta' => $this->serializer->normalize($meta, null, ['groups' => ['task:list']]),
        ]);
    }

    #[Route('/own', name: '.own', methods: ['GET'])]
    public function own(
        Project $project,
        Request $request,
        TaskFetcher $taskFetcher,
        MemberFetcher $memberFetcher,
        InertiaService $inertia,
        TaskMetaProvider $taskMetaProvider,
    ): Response {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        $filter = Filter\Filter::forProject($project->getId()->getValue());

        $pagination = $taskFetcher->all(
            $filter->withAuthor($this->getUser()->getId()),
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 't.id'),
            $request->query->get('direction', 'asc')
        );

        $tasks = array_map(fn ($task) => TaskMapper::map($task), $pagination->getItems());
        $normalizedTasks = $this->serializer->normalize($tasks, null, ['groups' => ['task:list']]);

        $meta = new TaskMetaView(); // DTO
        $props = $taskMetaProvider->get();

        $meta->statuses = $props['statuses'];
        $meta->types = $props['types'];
        $meta->priorities = $props['priorities'];

        return $inertia->render($request, 'Work/Projects/Project/Tasks/Index', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'tasks' => $normalizedTasks,
            'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'lastPage' => ceil($pagination->getTotalItemCount() / 50),
                'total' => $pagination->getTotalItemCount(),
            ],
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 't.date'),
            'direction' => $request->query->get('direction', 'asc'),
            'meta' => $this->serializer->normalize($meta, null, ['groups' => ['task:list']]),
        ]);
    }

    #[Route('/create', name: 'work.projects.project.tasks.create', methods: ['GET', 'POST'])]
    public function create(
        Project $project,
        Request $request,
        Create\Handler $handler,
        InertiaService $inertia,
    ): Response {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        if ($request->isMethod('GET')) {
            // Віддаємо стартові дані для форми Vue
            return $inertia->render($request, 'Work/Projects/Project/Tasks/Create', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'defaults' => [
                    'project' => $project->getId()->getValue(),
                    'member' => (string) $this->getUser()->getId(),
                    'type' => Type::NONE,
                    'priority' => 2,
                    'names' => [''], // можна початковий масив назв
                    'content' => '',
                    'parent' => $request->query->getInt('parent', (int) null),
                    'plan' => null,
                ],
                'types' => [
                    ['value' => Type::NONE, 'label' => 'None'],
                    ['value' => Type::ERROR, 'label' => 'Error'],
                    ['value' => Type::FEATURE, 'label' => 'Feature'],
                ],
                'priorities' => [
                    ['value' => 1, 'label' => 'Low'],
                    ['value' => 2, 'label' => 'Normal'],
                    ['value' => 3, 'label' => 'High'],
                    ['value' => 4, 'label' => 'Extra'],
                ],
            ]);
        }

        $data = json_decode($request->getContent(), true);

        $command = new Create\Command(
            $project->getId()->getValue(),
            $this->getUser()->getId()
        );

        $command->names = $data['names'] ?? [];
        $command->content = $data['content'] ?? null;
        $command->parent = $data['parent'] ?? null;
        $command->plan = isset($data['plan']) ? new \DateTimeImmutable($data['plan']) : null;
        $command->type = $data['type'] ?? Type::NONE;
        $command->priority = $data['priority'] ?? 2;

        try {
            $handler->handle($command);

            return $this->json(['success' => true], 201);
        } catch (\DomainException $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
