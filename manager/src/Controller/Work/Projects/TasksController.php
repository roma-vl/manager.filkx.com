<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects;

use App\Infrastructure\Inertia\InertiaService;
use App\Model\Comment\UseCase\Comment;
use App\Model\Work\Entity\Projects\Task\Progress;
use App\Model\Work\Entity\Projects\Task\Status;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\Type;
use App\Model\Work\UseCase\Projects\Task\ChildOf;
use App\Model\Work\UseCase\Projects\Task\Edit;
use App\Model\Work\UseCase\Projects\Task\Executor;
use App\Model\Work\UseCase\Projects\Task\Files;
use App\Model\Work\UseCase\Projects\Task\Move;
use App\Model\Work\UseCase\Projects\Task\Plan;
use App\Model\Work\UseCase\Projects\Task\Remove;
use App\Model\Work\UseCase\Projects\Task\Start;
use App\Model\Work\UseCase\Projects\Task\Status as UseCaseStatus;
use App\Model\Work\UseCase\Projects\Task\Type as UseCaseType;
use App\Model\Work\UseCase\Projects\Task\Priority as UseCasePriority;
use App\Model\Work\UseCase\Projects\Task\Progress as UseCaseProgress;
use App\Model\Work\UseCase\Projects\Task\Take;
use App\Model\Work\UseCase\Projects\Task\TakeAndStart;
use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\ReadModel\Work\Projects\Action\ActionFetcher;
use App\ReadModel\Work\Projects\Action\Feed\Feed;
use App\ReadModel\Work\Projects\Task\CommentFetcher;
use App\ReadModel\Work\Projects\Task\Filter;
use App\ReadModel\Work\Projects\Task\TaskFetcher;
use App\Security\Voter\Work\Projects\TaskAccess;
use App\Controller\ErrorHandler;
use App\Service\Uploader\FileUploader;
use cebe\markdown\MarkdownExtra;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work/projects/tasks', name: 'work.tasks')]
class TasksController extends AbstractController
{
    private const PER_PAGE = 10;
    public function __construct(
        private readonly ErrorHandler $errors,
        private readonly MarkdownExtra $markdown,
    )
    {
    }

    #[Route('', name: '', methods: ['GET'])]
    public function index(
        Request $request,
        TaskFetcher $taskFetcher,
        MemberFetcher $memberFetcher,
        InertiaService $inertia,
    ): Response {
        $filter = $this->isGranted('ROLE_WORK_MANAGE_PROJECTS')
            ? Filter\Filter::all()
            : Filter\Filter::all()->withMember((string) $this->getUser()->getId());

        $filter = $filter
            ->withType($request->query->get('type'))
            ->withText($request->query->get('text'))
            ->withPriority($request->query->get('priority'))
            ->withStatus($request->query->get('status'))
            ->withProject($request->query->get('project'))
            ->withExecutor($request->query->get('executor'))
            ->withAuthor($request->query->get('author'))
            ->withRoots($request->query->get('roots'));

        $pagination = $taskFetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 't.id'),
            $request->query->get('direction', 'asc')
        );

        return $inertia->render($request, 'Work/Projects/Tasks/Index', [
            'tasks' => array_map(fn ($task) => [
                'id' => $task['id'],
                'name' => $task['name'],
                'project_id' => $task['project_id'],
                'project_name' => $task['project_name'],
                'author_id' => $task['author_id'],
                'author_name' => $task['author_name'],
                'status' => $task['status'],
                'priority' => $task['priority'],
                'progress' => $task['progress'],
                'date' => $task['date'],
                'plan_date' => $task['plan_date'],
                'executors' => array_map(fn ($exec) => [
                    'task_id' => $exec['task_id'],
                    'name' => $exec['name'],
                ], $task['executors']),
                'parent' => $task['parent'] ?? null,
                'type' => $task['type'],
            ], $pagination->getItems()),
            'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'lastPage' => ceil($pagination->getTotalItemCount() / 50),
                'total' => $pagination->getTotalItemCount(),
            ],
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 't.date'),
            'direction' => $request->query->get('direction', 'asc'),
            'statuses' => [
                ['id' => Status::NEW, 'name' => 'NEW'],
                ['id' => Status::WORKING, 'name' => 'WORKING'],
                ['id' => Status::HELP, 'name' => 'HELP'],
                ['id' => Status::CHECKING, 'name' => 'CHECKING'],
                ['id' => Status::REJECTED, 'name' => 'REJECTED'],
                ['id' => Status::DONE, 'name' => 'DONE'],
            ],
            'type' => [
                ['id' => Type::NONE, 'name' => 'NONE'],
                ['id' => Type::ERROR, 'name' => 'ERROR'],
                ['id' => Type::FEATURE, 'name' => 'FEATURE'],
            ],
            'priority' => [
                ['id' => 1, 'name' => 'LOW'],
                ['id' => 2, 'name' => 'NORMAL'],
                ['id' => 3, 'name' => 'FEATURE'],
                ['id' => 4, 'name' => 'HIGH'],
                ['id' => 5, 'name' => 'CRITICAL'],
                ['id' => 6, 'name' => 'BLOCKER'],
            ]
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
        Request $request,
        TaskFetcher $taskFetcher,
        MemberFetcher $memberFetcher,
        InertiaService $inertia
    ): Response
    {
        $filter = Filter\Filter::all();

        $filter = $filter
            ->withType($request->query->get('type'))
            ->withText($request->query->get('text'))
            ->withPriority($request->query->get('priority'))
            ->withStatus($request->query->get('status'))
            ->withProject($request->query->get('project'))
            ->withExecutor($request->query->get('executor'))
            ->withAuthor($request->query->get('author'))
            ->withRoots($request->query->get('roots'));

        $pagination = $taskFetcher->all(
            $filter->withExecutor($this->getUser()->getId()),
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 't.id'),
            $request->query->get('direction', 'asc')
        );

        return $inertia->render($request, 'Work/Projects/Tasks/Index', [
            'tasks' => array_map(fn ($task) => [
                'id' => $task['id'],
                'name' => $task['name'],
                'project_id' => $task['project_id'],
                'project_name' => $task['project_name'],
                'author_id' => $task['author_id'],
                'author_name' => $task['author_name'],
                'status' => $task['status'],
                'priority' => $task['priority'],
                'progress' => $task['progress'],
                'date' => $task['date'],
                'plan_date' => $task['plan_date'],
                'executors' => array_map(fn ($exec) => [
                    'task_id' => $exec['task_id'],
                    'name' => $exec['name'],
                ], $task['executors']),
                'parent' => $task['parent'] ?? null,
                'type' => $task['type'],
            ], $pagination->getItems()),
            'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'lastPage' => ceil($pagination->getTotalItemCount() / 50),
                'total' => $pagination->getTotalItemCount(),
            ],
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 't.date'),
            'direction' => $request->query->get('direction', 'asc'),
            'statuses' => [
                ['id' => Status::NEW, 'name' => 'NEW'],
                ['id' => Status::WORKING, 'name' => 'WORKING'],
                ['id' => Status::HELP, 'name' => 'HELP'],
                ['id' => Status::CHECKING, 'name' => 'CHECKING'],
                ['id' => Status::REJECTED, 'name' => 'REJECTED'],
                ['id' => Status::DONE, 'name' => 'DONE'],
            ],
            'type' => [
                ['id' => Type::NONE, 'name' => 'NONE'],
                ['id' => Type::ERROR, 'name' => 'ERROR'],
                ['id' => Type::FEATURE, 'name' => 'FEATURE'],
            ],
            'priority' => [
                ['id' => 1, 'name' => 'LOW'],
                ['id' => 2, 'name' => 'NORMAL'],
                ['id' => 3, 'name' => 'FEATURE'],
                ['id' => 4, 'name' => 'HIGH'],
                ['id' => 5, 'name' => 'CRITICAL'],
                ['id' => 6, 'name' => 'BLOCKER'],
            ]
        ]);
    }

    #[Route('/own', name: '.own', methods: ['GET'])]
    public function own(
        Request $request,
        TaskFetcher $taskFetcher,
        MemberFetcher $memberFetcher,
        InertiaService $inertia
    ): Response
    {
        $filter = Filter\Filter::all();

        $filter = $filter
            ->withType($request->query->get('type'))
            ->withText($request->query->get('text'))
            ->withPriority($request->query->get('priority'))
            ->withStatus($request->query->get('status'))
            ->withProject($request->query->get('project'))
            ->withExecutor($request->query->get('executor'))
            ->withAuthor($request->query->get('author'))
            ->withRoots($request->query->get('roots'));

        $pagination = $taskFetcher->all(
            $filter->withAuthor($this->getUser()->getId()),
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 't.id'),
            $request->query->get('direction', 'asc')
        );

        return $inertia->render($request, 'Work/Projects/Tasks/Index', [
            'tasks' => array_map(fn ($task) => [
                'id' => $task['id'],
                'name' => $task['name'],
                'project_id' => $task['project_id'],
                'project_name' => $task['project_name'],
                'author_id' => $task['author_id'],
                'author_name' => $task['author_name'],
                'status' => $task['status'],
                'priority' => $task['priority'],
                'progress' => $task['progress'],
                'date' => $task['date'],
                'plan_date' => $task['plan_date'],
                'executors' => array_map(fn ($exec) => [
                    'task_id' => $exec['task_id'],
                    'name' => $exec['name'],
                ], $task['executors']),
                'parent' => $task['parent'] ?? null,
                'type' => $task['type'],
            ], $pagination->getItems()),
            'members' => $this->mapMembers($memberFetcher->activeGroupedList()),
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'lastPage' => ceil($pagination->getTotalItemCount() / 50),
                'total' => $pagination->getTotalItemCount(),
            ],
            'filters' => $request->query->all(),
            'sort' => $request->query->get('sort', 't.date'),
            'direction' => $request->query->get('direction', 'asc'),
            'statuses' => [
                ['id' => Status::NEW, 'name' => 'NEW'],
                ['id' => Status::WORKING, 'name' => 'WORKING'],
                ['id' => Status::HELP, 'name' => 'HELP'],
                ['id' => Status::CHECKING, 'name' => 'CHECKING'],
                ['id' => Status::REJECTED, 'name' => 'REJECTED'],
                ['id' => Status::DONE, 'name' => 'DONE'],
            ],
            'type' => [
                ['id' => Type::NONE, 'name' => 'NONE'],
                ['id' => Type::ERROR, 'name' => 'ERROR'],
                ['id' => Type::FEATURE, 'name' => 'FEATURE'],
            ],
            'priority' => [
                ['id' => 1, 'name' => 'LOW'],
                ['id' => 2, 'name' => 'NORMAL'],
                ['id' => 3, 'name' => 'FEATURE'],
                ['id' => 4, 'name' => 'HIGH'],
                ['id' => 5, 'name' => 'CRITICAL'],
                ['id' => 6, 'name' => 'BLOCKER'],
            ]
        ]);
    }


    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(
        Task $task,
        Request $request,
        Edit\Handler $handler,
        InertiaService $inertia
    ): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

            if ($request->isMethod('GET')) {
                return $inertia->render($request, 'Work/Projects/Tasks/Edit', [
                    'task' => [
                        'id' => $task->getId()->getValue(),
                        'name' => $task->getName(),
                        'content' => $task->getContent(),
                    ],
                ]);
            }
        $data = json_decode($request->getContent(), true);
        $command = Edit\Command::fromTask($this->getUser()->getId(), $task);

        $command->name = $data['name'] ?? [];
        $command->content = $data['content'] ?? null;


        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.tasks.show', ['id' => $task->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    /**
     * @Route("/{id}/files", name=".files")
     * @param Task $task
     * @param Request $request
     * @param Files\Add\Handler $handler
     * @param FileUploader $uploader
     * @return Response
     */
    public function files(Task $task, Request $request, Files\Add\Handler $handler, FileUploader $uploader): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Files\Add\Command($this->getUser()->getId(), $task->getId()->getValue());

        $form = $this->createForm(Files\Add\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = [];
            foreach ($form->get('files')->getData() as $file) {
                $uploaded = $uploader->upload($file);
                $files[] = new Files\Add\File(
                    $uploaded->getPath(),
                    $uploaded->getName(),
                    $uploaded->getSize()
                );
            }
            $command->files = $files;
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/tasks/files.html.twig', [
            'project' => $task->getProject(),
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/files/{file_id}/delete", name=".files.delete", methods={"POST"})
     * @ParamConverter("member", options={"id" = "member_id"})
     * @param Task $task
     * @param string $file_id
     * @param Request $request
     * @param Files\Remove\Handler $handler
     * @return Response
     */
    public function fileDelete(Task $task, string $file_id, Request $request, Files\Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete-file', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Files\Remove\Command($this->getUser()->getId(), $task->getId()->getValue(), $file_id);

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
    }

    /**
     * @Route("/{id}/child", name=".child")
     * @param Task $task
     * @param Request $request
     * @param ChildOf\Handler $handler
     * @return Response
     */
    public function childOf(Task $task, Request $request, ChildOf\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = ChildOf\Command::fromTask($this->getUser()->getId(), $task);

        $form = $this->createForm(ChildOf\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/tasks/child.html.twig', [
            'project' => $task->getProject(),
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/assign', name: '.assign', methods: ['GET', 'POST'])]
    public function assign(
        Task $task,
        Request $request,
        Executor\Assign\Handler $handler,
        MemberFetcher $members,
        InertiaService $inertia
    ): Response
    {
        $project = $task->getProject();
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Executor\Assign\Command($this->getUser()->getId(), $task->getId()->getValue());
        $selectedMembers = [];
        foreach ($task->getExecutors() as $executor) {
            $selectedMembers[] = $executor->getId()->getValue();
        }
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Tasks/Assign', [
                'task' => [
                    'id' => $task->getId()->getValue(),
                    'name' => $task->getName(),
                    'status' => $task->getStatus()->getName(),
                ],
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'members' => array_map(static fn($member) => [
                    'id' => $member['id'],
                    'name' => $member['name'],
                ], $members->activeDepartmentListForProject($project->getId()->getValue())),

                'selectedMembers' => $selectedMembers,
            ]);
        }

        $data = json_decode($request->getContent(), true);
        $command->members = $data['members'] ?? [];

        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.tasks.show', ['id' => $task->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    #[Route('/{id}/revoke/{member_id}', name: '.revoke', methods: ['POST'])]
    public function revoke(
        Task $task,
        string $member_id,
        Request $request,
        Executor\Revoke\Handler $handler,
        MemberFetcher $members,
    ): Response {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        // ⚠️ Тут ти сам ручками витягуєш Member
        $member = $members->find($member_id);

        if (!$member) {
            throw $this->createNotFoundException("Member not found");
        }

        $command = new Executor\Revoke\Command(
            $this->getUser()->getId(),
            $task->getId()->getValue(),
            $member->getId()->getValue()
        );

        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.tasks.show', ['id' => $task->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * @Route("/{id}/take", name=".take", methods={"POST"})
     * @param Task $task
     * @param Request $request
     * @param Take\Handler $handler
     * @return Response
     */
    public function take(Task $task, Request $request, Take\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('take', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Take\Command($this->getUser()->getId(), $task->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
    }

    /**
     * @Route("/{id}/take/start", name=".take_and_start", methods={"POST"})
     * @param Task $task
     * @param Request $request
     * @param TakeAndStart\Handler $handler
     * @return Response
     */
    public function takeAndStart(Task $task, Request $request, TakeAndStart\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('take-and-start', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new TakeAndStart\Command($this->getUser()->getId(), $task->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
    }

    /**
     * @Route("/{id}/start", name=".start", methods={"POST"})
     * @param Task $task
     * @param Request $request
     * @param Start\Handler $handler
     * @return Response
     */
    public function start(Task $task, Request $request, Start\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('start', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Start\Command($this->getUser()->getId(), $task->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
    }

    /**
     * @Route("/{id}/move", name=".move")
     * @param Task $task
     * @param Request $request
     * @param Move\Handler $handler
     * @return Response
     */
    public function move(Task $task, Request $request, Move\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = Move\Command::fromTask($this->getUser()->getId(), $task);

        $form = $this->createForm(Move\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/tasks/move.html.twig', [
            'project' => $task->getProject(),
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/plan', name: '.plan', methods: ['GET', 'POST'])]
    public function plan(
        Task $task,
        Request $request,
        Plan\Set\Handler $handler,
        InertiaService $inertia
    ): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Tasks/Plan', [
                'task' => [
                    'id' => $task->getId()->getValue(),
                    'name' => $task->getName(),
                    'plan_date' => $task->getPlanDate(),
                ],
            ]);
        }
        $data = json_decode($request->getContent(), true);
        $command = Plan\Set\Command::fromTask($this->getUser()->getId(), $task);


        $command->date = $data['plan_date'] ?new \DateTimeImmutable($data['plan_date']) : '';

        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.tasks.show', ['id' => $task->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }

    }

    /**
     * @Route("/{id}/plan/remove", name=".plan.remove", methods={"POST"})
     * @param Task $task
     * @param Request $request
     * @param Plan\Remove\Handler $handler
     * @return Response
     */
    public function removePlan(Task $task, Request $request, Plan\Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove-plan', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Plan\Remove\Command($this->getUser()->getId(), $task->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
    }

    /**
     * @Route("/{id}/delete", name=".delete", methods={"POST"})
     * @param Task $task
     * @param Request $request
     * @param Remove\Handler $handler
     * @return Response
     */
    public function delete(Task $task, Request $request, Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::DELETE, $task);

        $command = new Remove\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks');
    }

    #[Route('/{id}', name: '.show', methods: ['GET'])]
    public function show(
        Request $request,
        Task $task,
        TaskFetcher $tasks,
        MemberFetcher $members,
        InertiaService $inertia
    ): Response {
        $this->denyAccessUnlessGranted(TaskAccess::VIEW, $task);

        if (!$member = $members->find($this->getUser()->getId())) {
            throw $this->createAccessDeniedException();
        }

        $content = strip_tags($this->markdown->parse($task->getContent()),
            '<p><ul><ol><li><strong><em><code><pre><blockquote><a><h1><h2><h3><br>');


        return $inertia->render($request, 'Work/Projects/Tasks/Show', [
            'project' => [
                'id' => $task->getProject()->getId()->getValue(),
                'name' => $task->getProject()->getName(),
            ],
            'task' => [
                'id' => $task->getId()->getValue(),
                'name' => $task->getName(),
                'status' => $task->getStatus()->getName(),
                'priority' => $task->getPriority(),
                'type' => $task->getType()->getName(),
                'author' => [
                    'id' => $task->getAuthor()->getId()->getValue(),
                    'name' => $task->getAuthor()->getName()->getFull(),
                ],
                'executors' => array_map(fn ($exec) => [
                    'id' => $exec->getId()->getValue(),
                    'name' => $exec->getName()->getFull(),
                ], $task->getExecutors()),
                'progress' => $task->getProgress(),
                'content' => $content,
                'plan_date' => $task->getPlanDate()?->format('Y-m-d H:i:s'),
                'start_date' => $task->getStartDate()?->format('Y-m-d H:i:s'),
                'date' => $task->getDate()?->format('Y-m-d H:i:s'),
            ],
            'member' => [
                'id' => $member->getId()->getValue(),
                'name' => $member->getName(),
            ],

            'children' => array_map(fn ($task) => [
                'id' => $task['id'],
                'name' => $task['name'],
                'project_id' => $task['project_id'],
                'project_name' => $task['project_name'],
                'status' => $task['status'],
                'priority' => $task['priority'],
                'progress' => $task['progress'],
                'date' => $task['date'],
                'plan_date' => $task['plan_date'],
                'executors' => array_map(fn ($exec) => [
                    'task_id' => $exec['task_id'],
                    'name' => $exec['name'],
                ], $task['executors']),
                'parent' => $task['parent'] ?? null,
                'type' => $task['type'],
            ], $tasks->childrenOf($task->getId()->getValue())),

            'statuses' => [
                ['id' => Status::NEW, 'name' => 'NEW'],
                ['id' => Status::WORKING, 'name' => 'WORKING'],
                ['id' => Status::HELP, 'name' => 'HELP'],
                ['id' => Status::CHECKING, 'name' => 'CHECKING'],
                ['id' => Status::REJECTED, 'name' => 'REJECTED'],
                ['id' => Status::DONE, 'name' => 'DONE'],
            ],
            'types' => [
                ['id' => Type::NONE, 'name' => 'NONE'],
                ['id' => Type::ERROR, 'name' => 'ERROR'],
                ['id' => Type::BUG, 'name' => 'BUG'],
                ['id' => Type::FEATURE, 'name' => 'FEATURE'],
                ['id' => Type::TASK, 'name' => 'TASK'],
                ['id' => Type::SUPPORT, 'name' => 'SUPPORT'],
            ],

            'priorities' => [
                ['id' => 1, 'name' => 'LOW'],
                ['id' => 2, 'name' => 'NORMAL'],
                ['id' => 3, 'name' => 'FEATURE'],
                ['id' => 4, 'name' => 'HIGH'],
                ['id' => 5, 'name' => 'CRITICAL'],
                ['id' => 6, 'name' => 'BLOCKER'],
            ],
            'progress' => [
                ['id' => Progress::PROGRESS_0, 'name' => Progress::PROGRESS_0],
                ['id' => Progress::PROGRESS_25, 'name' => Progress::PROGRESS_25],
                ['id' => Progress::PROGRESS_50, 'name' => Progress::PROGRESS_50],
                ['id' => Progress::PROGRESS_75, 'name' => Progress::PROGRESS_75],
                ['id' => Progress::PROGRESS_100, 'name' => Progress::PROGRESS_100],
            ],

        ]);
    }
    #[Route('/{id}/status', name: '.status', methods: ['POST'])]
    public function changeStatus(Task $task, Request $request, UseCaseStatus\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $data = json_decode($request->getContent(), true);
        $command = new UseCaseStatus\Command(
            $this->getUser()->getId(),
            $task->getId()->getValue(),
        );

        $command->status = $data['status'] ?: '';

        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.tasks.show', ['id' => $task->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    #[Route('/{id}/type', name: '.type', methods: ['POST'])]
    public function changeType(Task $task, Request $request, UseCaseType\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $data = json_decode($request->getContent(), true);

        $command = new UseCaseType\Command(
            $this->getUser()->getId(),
            $task->getId()->getValue()
        );

        $command->type = $data['type'] ?? '';

        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.tasks.show', ['id' => $task->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    #[Route('/{id}/priority', name: '.priority', methods: ['POST'])]
    public function changePriority(Task $task, Request $request, UseCasePriority\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $data = json_decode($request->getContent(), true);

        $command = new UseCasePriority\Command(
            $this->getUser()->getId(),
            $task->getId()->getValue()
        );
        $command->priority = $data['priority'] ?? '';

        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.tasks.show', ['id' => $task->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    #[Route('/{id}/progress', name: '.progress', methods: ['POST'])]
    public function changeProgress(Task $task, Request $request, UseCaseProgress\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $data = json_decode($request->getContent(), true);
        $command = new UseCaseProgress\Command(
            $this->getUser()->getId(),
            $task->getId()->getValue()
        );

        $command->progress = (int)($data['progress'] ?? 0);

        try {
            $handler->handle($command);
            return $this->redirectToRoute('work.tasks.show', ['id' => $task->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            return $this->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}

