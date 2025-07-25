<?php

namespace App\Controller\Widgets;
use App\Controller\BaseController;
use App\Infrastructure\Inertia\InertiaService;
use App\ReadModel\Work\Projects\Calendar\CalendarFetcher;
use App\ReadModel\Work\Projects\Calendar\Query\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
class CalendarController extends BaseController
{
    public function __construct(private readonly CalendarFetcher $calendar)
    {
    }

    #[Route('/api/widgets/work/projects/calendar', name: 'api.widgets.work.projects.calendar')]
    public function widget(Request $request): JsonResponse
    {
        if (!$this->isGranted('ROLE_WORK_MANAGE_PROJECTS')) {
            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        $now = new \DateTimeImmutable();
        $userId = $this->getUser()->getId();
        $result = $this->calendar->byWeek($now, $userId);

        return new JsonResponse([
            'dates' => array_map(fn($d) => $d->format('Y-m-d'), iterator_to_array(
                new \DatePeriod($result->start, new \DateInterval('P1D'), $result->end)
            )),
            'now' => $now->format('Y-m-d'),
            'items' => $result->items,
        ]);
    }

}
