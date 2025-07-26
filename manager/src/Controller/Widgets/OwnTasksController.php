<?php

declare(strict_types=1);

namespace App\Controller\Widgets;

use App\ReadModel\Work\Projects\Task\TaskFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OwnTasksController extends AbstractController
{
    public function __construct(private readonly TaskFetcher $fetcher)
    {
    }

    #[Route('/api/widgets/work/projects/own-tasks', name: 'api.widgets.work.projects.own_tasks', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        if (!$this->isGranted('ROLE_USER')) {
            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        $user = $this->getUser();
        $tasks = $this->fetcher->lastOwn($user->getId(), 10);

        return $this->json(['tasks' => $tasks]);
    }
}
