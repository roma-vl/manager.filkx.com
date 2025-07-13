<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\ErrorHandler;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\EntityNotFoundException;
use App\Model\User\UseCase\Reset;
use App\ReadModel\User\UserFetcher;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetController extends AbstractController
{
    public function __construct(
        private readonly ErrorHandler $errors,
    ) {}

    #[Route('/reset', name: 'auth.reset', methods: ['GET', 'POST'])]
    public function request(
        Request $request,
        InertiaService $inertia,
        Reset\Request\Handler $handler,
        ValidatorInterface $validator
    ): Response {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            $command = new Reset\Request\Command();
            $command->email = $data['email'] ?? '';

            $violations = $validator->validate($command);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $inertia->render($request, 'Auth/Reset/Request', [
                    'errors' => $errors,
                ]);
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
        ValidatorInterface $validator
    ): Response {
        if (!$users->existsByResetToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token.');
            return $inertia->redirect('/');
        }

        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            $command = new Reset\Reset\Command($token);
            $command->password = $data['password'] ?? '';

            $violations = $validator->validate($command);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[$violation->getPropertyPath()] = $violation->getMessage();
                }
                return $inertia->render($request, 'Auth/Reset/Reset', [
                    'errors' => $errors,
                    'token' => $token,
                ]);
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
