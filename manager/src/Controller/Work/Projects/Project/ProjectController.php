<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project;

use App\Annotation\Guid;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Security\Voter\Work\Projects\ProjectAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/work/projects/{id}', name: 'work.projects.project')]
final class ProjectController extends AbstractController
{
    #[Route('', name: '.show', requirements: ['id' => Guid::UUID_REGEX], methods: ['GET'])]
    public function show(Request $request, Project $project, InertiaService $inertia): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        return $inertia->render($request, 'Work/Projects/Project/Show', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
                'status' => $project->getStatus()->getName(),
                'sort' => $project->getSort(),
            ],
        ]);
    }
}
