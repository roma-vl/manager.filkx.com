<?php

namespace App\Controller\Auth\OAuth;


use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GoogleController extends AbstractController
{
    #[Route('/oauth/google', name: 'oauth.google')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google_main')
            ->redirect(['email', 'profile']);
    }

    #[Route('/oauth/google/check', name: 'oauth.google_check')]
    public function check(): Response
    {
        // після успішного логіну Security listener автоматично обробить користувача
        return $this->redirectToRoute('home');
    }
}
