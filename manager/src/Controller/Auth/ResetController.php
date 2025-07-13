<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\BaseController;
use App\Controller\ErrorHandler;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\EntityNotFoundException;
use App\Model\User\UseCase\Reset;
use App\ReadModel\User\UserFetcher;
use App\Service\CommandFactory;
use DomainException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ResetController extends BaseController
{
    public function __construct(
        private readonly ErrorHandler $errors,
    ) {}

    #[Route('/reset', name: 'auth.reset', methods: ['GET', 'POST'])]
    public function request(
        Request $request,
        InertiaService $inertia,
        Reset\Request\Handler $handler,
        CommandFactory $commandFactory
    ): Response {
        if ($request->isMethod('POST')) {

            $command = new Reset\Request\Command();
            $errors = $commandFactory->createFromRequest($request, $command);

            if ($errors) {
                return $this->renderWithErrors($request, $inertia, 'Auth/SignUp', $errors);
            }

            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $inertia->redirect('/');
            } catch (EntityNotFoundException|DomainException $e) {
                return $inertia->render($request, 'Auth/Reset/Request', [
                    'errors' => ['email' => $e->getMessage()],
                ]);
            }
        }

        return $inertia->render($request, 'Auth/Reset/Request');
    }

    #[Route('/reset/{token}', name: 'auth.reset.reset', methods: ['GET', 'POST'])]
    public function reset(
        string $token,
        Request $request,
        InertiaService $inertia,
        Reset\Reset\Handler $handler,
        UserFetcher $users,
        CommandFactory $commandFactory
    ): Response {
        if (!$users->existsByResetToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token.');
            return $inertia->redirect('/');
        }

        if ($request->isMethod('POST')) {
            $command = new Reset\Reset\Command($token);
            $errors = $commandFactory->createFromRequest($request, $command);

            if ($errors) {
                return $this->renderWithErrors($request, $inertia, 'Auth/SignUp', $errors);
            }

            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password is successfully changed.');
                return $inertia->redirect('/');
            } catch (DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
                return $inertia->location($request->getUri());
            }
        }

        return $inertia->render($request, 'Auth/Reset/Reset', [
            'token' => $token,
        ]);
    }
}
