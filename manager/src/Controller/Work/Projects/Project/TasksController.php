<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project;

use App\Builder\Work\Task\TaskMetaBuilder;
use App\Controller\ErrorHandler;
use App\DTO\Work\Task\TaskMetaView;
use App\Infrastructure\Inertia\InertiaService;
use App\Infrastructure\Pagination\PaginationViewFactory;
use App\Mapper\Work\TaskMapper;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Task\Status;
use App\Model\Work\Entity\Projects\Task\Type;
use App\Model\Work\UseCase\Projects\Task\Create;
use App\Normalizer\TaskListNormalizer;
use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\ReadModel\Work\Projects\Task\Filter;
use App\ReadModel\Work\Projects\Task\TaskFetcher;
use App\ReadModel\Work\Projects\Task\TaskMetaProvider;
use App\Security\Voter\Work\Projects\ProjectAccess;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/work/projects/{id}/tasks', name: 'work.projects.project.tasks')]
class TasksController extends AbstractController
{
    private const PER_PAGE = 10;

    public function __construct(
        private readonly TaskFetcher $tasks,
        private readonly ErrorHandler $errors,
        private readonly PaginationViewFactory $paginationFactory
    )
    {
    }

    /**
     * @throws Exception
     */
    #[Route('', name: '', methods: ['GET'])]
    public function index(
        Project $project,
        Request $request,
        MemberFetcher $memberFetcher,
        InertiaService $inertia,
        TaskListNormalizer  $taskListNormalizer,
        TaskMetaBuilder $taskMetaBuilder,
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

        return $inertia->render($request, 'Work/Projects/Project/Tasks/Index', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'tasks' => $taskListNormalizer->normalize($pagination->getItems()),
            'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            'pagination' => $this->paginationFactory->create($pagination, self::PER_PAGE),
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 't.id'),
            'direction' => $request->query->get('direction', 'asc'),
            'meta' => $taskMetaBuilder->build(),
        ]);
    }

    #[Route('/me', name: '.me', methods: ['GET'])]
    public function me(
        Project $project,
        Request $request,
        TaskFetcher $taskFetcher,
        MemberFetcher $memberFetcher,
        InertiaService $inertia,
        TaskListNormalizer  $taskListNormalizer,
        TaskMetaBuilder $taskMetaBuilder,
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

        return $inertia->render($request, 'Work/Projects/Project/Tasks/Index', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'tasks' => $taskListNormalizer->normalize($pagination->getItems()),
            'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            'pagination' => $this->paginationFactory->create($pagination, self::PER_PAGE),
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 't.date'),
            'direction' => $request->query->get('direction', 'asc'),
            'meta' => $taskMetaBuilder->build(),
        ]);
    }

    #[Route('/own', name: '.own', methods: ['GET'])]
    public function own(
        Project $project,
        Request $request,
        TaskFetcher $taskFetcher,
        MemberFetcher $memberFetcher,
        InertiaService $inertia,
        TaskListNormalizer  $taskListNormalizer,
        TaskMetaBuilder $taskMetaBuilder,
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

        return $inertia->render($request, 'Work/Projects/Project/Tasks/Index', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'tasks' => $taskListNormalizer->normalize($pagination->getItems()),
            'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            'pagination' => $this->paginationFactory->create($pagination, self::PER_PAGE),
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 't.date'),
            'direction' => $request->query->get('direction', 'asc'),
            'meta' => $taskMetaBuilder->build(),
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
}
