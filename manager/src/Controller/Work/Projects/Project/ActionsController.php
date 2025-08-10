<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project;

use App\Infrastructure\Inertia\InertiaService;
use App\Model\Work\Entity\Projects\Project\Project;
use App\ReadModel\Work\Projects\Action\ActionFetcher;
use App\ReadModel\Work\Projects\Action\Filter;
use App\Security\Voter\Work\Projects\ProjectAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work/projects/{id}/actions', name: 'work.projects.project.actions')]
class ActionsController extends AbstractController
{
    private const PER_PAGE = 50;

    private $actions;

    public function __construct(ActionFetcher $actions)
    {
        $this->actions = $actions;
    }

    #[Route('', name: '', methods: ['GET'])]
    public function index(Project $project, Request $request, InertiaService $inertia): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        $filter = Filter::forProject($project->getId()->getValue());

        $pagination = $this->actions->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE
        );

        return $inertia->render($request, 'Work/Projects/Project/Actions', [
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'pagination' => [
                'items' => array_map(fn ($action) => [
                    'id' => $action['id'],
                    'task_id' => $action['task_id'],
                    'actor_id' => $action['actor_id'],
                    'date' => $action['date'],
                    'set_project_id' => $action['set_project_id'],
                    'set_name' => $action['set_name'],
                    'set_content' => $action['set_content'],
                    'set_file_id' => $action['set_file_id'],
                    'set_removed_file_id' => $action['set_removed_file_id'],
                    'set_type' => $action['set_type'],
                    'set_status' => $action['set_status'],
                    'set_progress' => $action['set_progress'],
                    'set_priority' => $action['set_priority'],
                    'set_parent_id' => $action['set_parent_id'],
                    'set_removed_parent' => $action['set_removed_parent'],
                    'set_plan' => $action['set_plan'],
                    'set_removed_plan' => $action['set_removed_plan'],
                    'set_executor_id' => $action['set_executor_id'],
                    'set_revoked_executor_id' => $action['set_revoked_executor_id'],
                    'task_name' => $action['task_name'],
                    'actor_name' => $action['actor_name'],
                    'project_id' => $action['project_id'],
                    'project_name' => $action['project_name'],
                    'set_executor_name' => $action['set_executor_name'],
                    'set_revoked_executor_name' => $action['set_revoked_executor_name'],
                    'set_project_name' => $action['set_project_name'],
                ], $pagination->getItems()),
                'pagination' => [
                    'current_page' => $pagination->getCurrentPageNumber(),
                    'total_pages' => $pagination->getPageCount(),
                    'total_items' => $pagination->getTotalItemCount(),
                    'per_page' => $pagination->getItemNumberPerPage(),
                ],
            ],
        ]);
    }
}
