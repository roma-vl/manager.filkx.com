<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\Work\Entity\Projects\Project\Department\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Project\Department\Create;
use App\Model\Work\UseCase\Projects\Project\Department\Edit;
use App\Model\Work\UseCase\Projects\Project\Department\Remove;
use App\ReadModel\Work\Projects\Project\DepartmentFetcher;
use App\Controller\ErrorHandler;
use App\Security\Voter\Work\Projects\ProjectAccess;
use App\Service\CommandFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work/projects/{id}/settings/departments', name: 'work.projects.project.settings.departments')]

class DepartmentsController extends AbstractController
{
    public function __construct(
        private ErrorHandler $errors,
    ) {}

    #[Route('', name: '')]
    public function index(
        Project $project,
        DepartmentFetcher $departments,
        Request $request,
        InertiaService $inertia
    ): Response {
         $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        return $inertia->render($request,'Work/Projects/Project/Settings/Departments/Index', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'departments' => $departments->allOfProject($project->getId()->getValue()),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(
        Project $project,
        Request $request,
        Create\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory
    ): Response {
         $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Project/Settings/Departments/Create', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
            ]);
        }

        $command = new Create\Command($project->getId()->getValue());

        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Projects/Create', ['errors' => $errors]);
        }

        try {
            $handler->handle($command);

            return $this->redirectToRoute('work.projects.project.settings.departments', ['id' => $project->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->render($request, 'Work/Projects/Project/Settings/Departments/Create', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'errors' => ['message' => $e->getMessage()],
            ]);
        }
    }

    #[Route('/{department_id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(
        Project $project,
        string $department_id,
        Request $request,
        Edit\Handler $handler,
        InertiaService $inertia,
        CommandFactory $commandFactory
    ): Response {
         $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        $department = $project->getDepartment(new Id($department_id));

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Project/Settings/Departments/Edit', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'department' => [
                    'id' => $department->getId()->getValue(),
                    'name' => $department->getName(),
                    // Додаткові поля за потребою
                ],
            ]);
        }

        $command = Edit\Command::fromDepartment($project, $department);
        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Projects/Create', ['errors' => $errors]);
        }

        try {
            $handler->handle($command);

            return $this->redirectToRoute('work.projects.project.settings.departments', ['id' => $project->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->render($request,'Work/Projects/Project/Settings/Departments/Edit', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'department' => [
                    'id' => $department->getId()->getValue(),
                    'name' => $department->getName(),
                ],
                'errors' => ['message' => $e->getMessage()],
            ]);
        }
    }

    #[Route('/{department_id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(
        Project $project,
        string $department_id,
        Request $request,
        Remove\Handler $handler
    ): Response {
        // $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        $department = $project->getDepartment(new Id($department_id));
        $command = new Remove\Command($project->getId()->getValue(), $department->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings.departments', ['id' => $project->getId()]);
    }

    #[Route('/{department_id}', name: '.show', requirements: ['department_id' => Guid::UUID_REGEX])]
    public function show(Project $project): Response
    {
        return $this->redirectToRoute('work.projects.project.settings.departments', ['id' => $project->getId()]);
    }
}
