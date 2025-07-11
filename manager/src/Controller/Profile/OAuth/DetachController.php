<?php

declare(strict_types=1);

namespace App\Controller\Profile\OAuth;

use App\Model\User\UseCase\Network\Detach\Command;
use App\Model\User\UseCase\Network\Detach\Handler;
use App\Controller\ErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile/oauth')]
final class DetachController extends AbstractController
{
    public function __construct(
        private readonly ErrorHandler $errors,
    ) {}

    #[Route('/detach/{network}/{identity}', name: 'profile.oauth.detach', methods: ['DELETE'])]
    public function detach(Request $request, string $network, string $identity, Handler $handler): Response
    {
        $token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('delete', $token)) {
            return $this->redirectToRoute('profile');
        }

        $command = new Command(
            $this->getUser()->getId(),
            $network,
            $identity,
        );

        try {
            $handler->handle($command);
            return $this->redirectToRoute('profile');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile');
        }
    }
}
