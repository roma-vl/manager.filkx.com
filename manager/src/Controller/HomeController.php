<?php

declare(strict_types=1);

namespace App\Controller;

use App\Infrastructure\Inertia\InertiaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('app/home.html.twig');
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(Request $request, InertiaService $inertia): Response
    {
        $inertia->shareFromSession($request);
        $inertia->share('auth', [
            'user' => ['id' => 1, 'name' => 'Рома'],
        ]);

        return $inertia->render($request, 'Dashboard', [
            'message' => 'Слава Inertia без Laravel!',
        ]);
    }

    #[Route('/submit', name: 'submit')]
    public function submit(Request $request, InertiaService $inertia): Response
    {
        $data = $request->request->all();

        if (empty($data['email'])) {
            return $inertia
                ->withErrors(['email' => 'Поле email є обовʼязковим.'])
                ->render($request, 'FormPage');
        }

        // все ок — редірект
        return $inertia->redirect('/dashboard');
    }

    #[Route('/submit-form', name: 'form.show')]
    public function showForm(Request $request, InertiaService $inertia): Response
    {
        return $inertia->render($request, 'FormPage');
    }

}
