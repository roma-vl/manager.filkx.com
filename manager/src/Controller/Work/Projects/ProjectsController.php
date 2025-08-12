<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects;

use App\Controller\BaseController;
use App\Controller\ErrorHandler;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\Work\UseCase\Projects\Project\Create;
use App\ReadModel\Work\Projects\Project\Filter;
use App\ReadModel\Work\Projects\Project\ProjectFetcher;
use App\Service\CommandFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/work/projects', name: 'work.projects')]
class ProjectsController extends BaseController
{
    private const PER_PAGE = 10;

    public function __construct(
        private readonly ErrorHandler $errors,
    ) {
    }

    #[Route('', name: '', methods: ['GET'])]
    public function index(
        Request $request,
        ProjectFetcher $fetcher,
        InertiaService $inertia,
        Security $security,
    ): Response {
        $filter = $this->isGranted('ROLE_WORK_MANAGE_PROJECTS')
            ? Filter\Filter::all()
            : Filter\Filter::forMember($this->getUser()->getId());

        $filter->name = $request->query->get('name');
        $filter->status = $request->query->get('status');
        $filter->account_id = $security->getUser()->getAccount()->getId()->getValue();
        $page = $request->query->getInt('page', 1);
        $sort = $request->query->get('sort', 'name');
        $direction = $request->query->get('direction', 'asc');

        $pagination = $fetcher->all(
            $filter,
            $page,
            self::PER_PAGE,
            $sort,
            $direction
        );

        return $inertia->render($request, 'Work/Projects/Index', [
            'projects' => array_map(fn ($project) => [
                'id' => $project['id'],
                'name' => $project['name'],
                'status' => $project['status'],
                'members_count' => $project['members_count'] ?? 0,
                'tasks_count' => $project['tasks_count'] ?? 0,
            ], $pagination->getItems()),
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'lastPage' => ceil($pagination->getTotalItemCount() / self::PER_PAGE),
                'total' => $pagination->getTotalItemCount(),
            ],
            'filters' => $request->query->all(),
            'sort' => $sort,
            'direction' => $direction,
            'statuses' => [
                ['id' => 'active', 'name' => 'Active'],
                ['id' => 'archived', 'name' => 'Archived'],
            ],
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_WORK_MANAGE_PROJECTS')]
    public function create(
        Request $request,
        ProjectFetcher $projects,
        Create\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory,
        Security $security,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_WORK_MANAGE_PROJECTS');
        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Create',
                ['project' => [
                    'sort' => $projects->getMaxSort() + 1,
                ]]);
        }

        $command = new Create\Command();
        $command->account = $security->getUser()->getAccount();
        $command->sort = $projects->getMaxSort() + 1;

        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Projects/Create', ['errors' => $errors]);
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Проєкт створено успішно.');

            return $inertia->redirect('/work/projects');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->redirect('/work/projects/create');
        }
    }
}
