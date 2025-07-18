<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Project\Archive;
use App\Model\Work\UseCase\Projects\Project\Edit;
use App\Model\Work\UseCase\Projects\Project\Reinstate;
use App\Model\Work\UseCase\Projects\Project\Remove;
use App\Controller\ErrorHandler;
use App\Service\CommandFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work/projects/{id}/settings', name: 'work.projects.project.settings')]
class SettingsController extends AbstractController
{
    public function __construct(
        private ErrorHandler $errors,
    ) {}

    #[Route('', name: '')]
    public function show(Project $project, Request $request, InertiaService $inertia): Response
    {
        // $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        return $inertia->render($request, 'Work/Projects/Project/Settings/Show', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
                'status' => $project->getStatus()->getName(),
                'archived' => $project->isArchived(),
                'active' => $project->isActive(),
            ],
        ]);
    }

    #[Route('/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Project $project, Request $request, Edit\Handler $handler, InertiaService $inertia, CommandFactory $commandFactory,): Response
    {
        // $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        $command = Edit\Command::fromProject($project);

        if ($request->isMethod('GET')) {
            return $inertia->render($request, 'Work/Projects/Project/Settings/Edit', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                    'sort' => $project->getSort(),
                ],
            ]);
        }

        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $inertia->render($request, 'Work/Projects/Project/Settings/Edit', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                    'sort' => $project->getSort(),
                ],
                'errors' => $errors,
            ]);
        }

        try {
            $handler->handle($command);

            return $this->redirectToRoute('work.projects.project.show', ['id' => $project->getId()]);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->render($request, 'Work/Projects/Project/Settings/Edit', [
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'errors' => ['message' => $e->getMessage()],
            ]);
        }
    }

    #[Route('/archive', name: '.archive', methods: ['POST'])]
    public function archive(Project $project, Request $request, Archive\Handler $handler): Response
    {
        // $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        $command = new Archive\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Project archived successfully.');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings', ['id' => $project->getId()]);
    }

    #[Route('/reinstate', name: '.reinstate', methods: ['POST'])]
    public function reinstate(Project $project, Request $request, Reinstate\Handler $handler): Response
    {
        // $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        $command = new Reinstate\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Project reinstate successfully.');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.project.settings', ['id' => $project->getId()]);
    }

    #[Route('/delete', name: '.delete', methods: ['POST'])]
    public function delete(Project $project, Request $request, Remove\Handler $handler): Response
    {
        // $this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

        $command = new Remove\Command($project->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects');
    }
}
