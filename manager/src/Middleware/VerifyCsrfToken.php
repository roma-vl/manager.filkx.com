<?php

// src/Middleware/VerifyCsrfToken.php
namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class VerifyCsrfToken
{
    public function __construct(
        private CsrfTokenManagerInterface $csrfTokenManager
    ) {}

    public function handle(Request $request, $next): Response
    {
        if ($request->isMethod('POST')) {
            $token = $request->request->get('_token') ?? $request->headers->get('X-CSRF-TOKEN');

            if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $token))) {
                return new Response('Invalid CSRF token', 419);
            }
        }

        return $next($request);
    }
}
