<?php

declare(strict_types=1);

namespace App\Controller\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FacebookController extends AbstractController
{
    #[Route('/oauth/facebook', name: 'oauth.facebook')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('facebook_main')
            ->redirect(['public_profile', 'email']); // додай 'email', якщо треба
    }

    #[Route('/oauth/facebook/check', name: 'oauth.facebook_check')]
    public function check(): Response
    {
        // Тут відбудеться редирект після успішного логіну
        // Логіка обробки у Security listener'і (див. нижче)
        return $this->redirectToRoute('home');
    }
}
