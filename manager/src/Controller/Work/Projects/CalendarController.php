<?php

declare(strict_types=1);

namespace App\Controller\Work\Projects;

use App\Controller\BaseController;
use App\Infrastructure\Inertia\InertiaService;
use App\ReadModel\Work\Projects\Calendar\CalendarFetcher;
use App\ReadModel\Work\Projects\Calendar\Query\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/work/projects/calendar', name: 'work.projects.calendar')]
class CalendarController extends BaseController
{
    public function __construct(
        private readonly InertiaService $inertia,
        private readonly CalendarFetcher $calendar,
    ) {}

    #[Route('', name: '', methods: ['GET'])]
    public function index(Request $request): Response|JsonResponse
    {
        $now = new \DateTimeImmutable();

        if ($this->isGranted('ROLE_WORK_MANAGE_PROJECTS')) {
            $query = Query::fromDate($now);
        } else {
            $query = Query::fromDate($now)->forMember($this->getUser()->getId());
        }

        $query->year = (int) $request->query->get('year', $query->year);
        $query->month = (int) $request->query->get('month', $query->month);

        $result = $this->calendar->byMonth($query);

        if ($request->headers->get('X-Inertia') === null && $request->isXmlHttpRequest()) {
            return new JsonResponse([
                'dates' => array_map(fn($d) => $d->format('Y-m-d'), iterator_to_array(
                    new \DatePeriod($result->start, new \DateInterval('P1D'), $result->end)
                )),
                'now' => $now->format('Y-m-d'),
                'result' => [
                    'month' => $result->month->format('Y-m'),
                    'items' => $result->items,
                ],
                'year' => $query->year,
                'month' => $query->month,
                'years' => range((int) date('Y') - 5, (int) date('Y') + 5),
                'next' => $result->month->modify('+1 month')->format('Y-m'),
                'prev' => $result->month->modify('-1 month')->format('Y-m'),
            ]);
        }

        return $this->inertia->render($request, 'Work/Projects/Calendar', [
            'dates' => array_map(fn($d) => $d->format('Y-m-d'), iterator_to_array(
                new \DatePeriod($result->start, new \DateInterval('P1D'), $result->end)
            )),
            'now' => $now->format('Y-m-d'),
            'result' => [
                'month' => $result->month->format('Y-m'),
                'items' => $result->items,
            ],
            'year' => $query->year,
            'month' => $query->month,
            'years' => range((int) date('Y') - 5, (int) date('Y') + 5),
            'next' => $result->month->modify('+1 month')->format('Y-m'),
            'prev' => $result->month->modify('-1 month')->format('Y-m'),
        ]);
    }
}
