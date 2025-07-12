<?php

// src/Security/LogoutSuccessHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    public function onLogoutSuccess(Request $request): RedirectResponse|JsonResponse
    {
        if ($request->headers->get('X-Inertia')) {
            return new JsonResponse([], 409, [
                'X-Inertia-Location' => $request->getSchemeAndHttpHost().'/login'
            ]);
        }

        return new RedirectResponse('/login');
    }
}
