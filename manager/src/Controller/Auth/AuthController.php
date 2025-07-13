<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Infrastructure\Inertia\InertiaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(
        Request $request,
        InertiaService $inertia,
        AuthenticationUtils $authenticationUtils,
    ): Response {
        if ($request->isMethod('POST')) {
            $error = $authenticationUtils->getLastAuthenticationError();

            if ($error) {
                $request->getSession()->getFlashBag()->add('error', $error->getMessageKey());
                return $inertia->location($request->getUri());
            }

            return $inertia->redirect('/');
        }

        return $inertia->render($request, 'Auth/Login', [
            'lastUsername' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        throw new \LogicException('This method will be intercepted by the logout key on your firewall.');
    }
}
