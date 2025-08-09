<?php

declare(strict_types=1);

namespace App\Controller;

use App\Infrastructure\Inertia\InertiaService;
use App\Service\CentrifugoPublisher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseController
{
    #[Route('/', name: 'home')]
    public function index(
        Request $request,
        CentrifugoPublisher $centrifugoPublisher,
        InertiaService $inertia
    ): Response
    {
        $userId = 'user:' . $this->getUser()->getId();
        $centrifugoPublisher->publish('chat:general', [
            'text' => 'Привіт з сервера!'
        ]);

        $centrifugoPublisher->publish($userId, [
            'text' => 'Приватне повідомлення для тебе!',
        ]);


//        return $inertia->render($request, 'Home', [
//            'message' => 'Inertia без Laravel!',
//        ]);
        return $inertia->redirect('dashboard');
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(Request $request, InertiaService $inertia): Response
    {
        return $inertia->render($request, 'Dashboard');
    }
}
