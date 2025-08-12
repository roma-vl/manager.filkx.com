<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects\Project;

use App\Infrastructure\Inertia\InertiaService;
use App\Model\Work\Entity\Projects\Project\Project;
use App\ReadModel\Work\Projects\Calendar\CalendarFetcher;
use App\ReadModel\Work\Projects\Calendar\Query\Query;
use App\Security\Voter\Work\Projects\ProjectAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work/projects/{id}/calendar', name: 'work.projects.project.calendar')]
class CalendarController extends AbstractController
{
    public function __construct(
        private readonly InertiaService $inertia,
        private readonly CalendarFetcher $calendar,
    ) {
    }

    #[Route('', name: '', methods: ['GET'])]
    public function index(Project $project, Request $request, Security $security,): Response|JsonResponse
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        $now = new \DateTimeImmutable();
        $query = Query::fromDate($now)->forProject($project->getId()->getValue());

        $query->account = $security->getUser()->getAccount();
        $query->year = (int) $request->query->get('year', $query->year);
        $query->month = (int) $request->query->get('month', $query->month);

        $result = $this->calendar->byMonth($query);

        if ($request->headers->get('X-Inertia') === null && $request->isXmlHttpRequest()) {
            return new JsonResponse([
                'dates' => array_map(fn ($d) => $d->format('Y-m-d'), iterator_to_array(
                    new \DatePeriod($result->start, new \DateInterval('P1D'), $result->end)
                )),
                'now' => $now->format('Y-m-d'),
                'result' => [
                    'month' => $result->month->format('Y-m'),
                    'items' => $result->items,
                ],
                'project' => [
                    'id' => $project->getId()->getValue(),
                    'name' => $project->getName(),
                ],
                'year' => $query->year,
                'month' => $query->month,
                'years' => range((int) date('Y') - 5, (int) date('Y') + 5),
                'next' => $result->month->modify('+1 month')->format('Y-m'),
                'prev' => $result->month->modify('-1 month')->format('Y-m'),
            ]);
        }

        return $this->inertia->render($request, 'Work/Projects/Project/Calendar', [
            'dates' => array_map(fn ($d) => $d->format('Y-m-d'), iterator_to_array(
                new \DatePeriod($result->start, new \DateInterval('P1D'), $result->end)
            )),
            'now' => $now->format('Y-m-d'),
            'result' => [
                'month' => $result->month->format('Y-m'),
                'items' => $result->items,
            ],
            'project' => [
                'id' => $project->getId()->getValue(),
                'name' => $project->getName(),
            ],
            'year' => $query->year,
            'month' => $query->month,
            'years' => range((int) date('Y') - 5, (int) date('Y') + 5),
            'next' => $result->month->modify('+1 month')->format('Y-m'),
            'prev' => $result->month->modify('-1 month')->format('Y-m'),
        ]);
    }
}
