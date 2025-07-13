<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\BaseController;
use App\Controller\ErrorHandler;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\User\UseCase\SignUp;
use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
use App\Security\UserProvider;
use App\Service\CommandFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SignUpController extends BaseController
{
    public function __construct(
        private readonly UserFetcher $users,
        private readonly ErrorHandler $errors,
        private readonly UserProvider $userProvider,
    ) {
    }

    #[Route('/signup', name: 'auth.signup', methods: ['GET', 'POST'])]
    public function request(
        Request $request,
        InertiaService $inertia,
        SignUp\Request\Handler $handler,
        CommandFactory $commandFactory,
    ): Response {
        if ($request->isMethod('POST')) {
            $command = new SignUp\Request\Command();
            $errors = $commandFactory->createFromRequest($request, $command);

            if ($errors) {
                return $this->renderWithErrors($request, $inertia, 'Auth/SignUp', $errors);
            }

            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');

                return $inertia->redirect('/');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());

                return $inertia->location($request->getUri());
            }
        }

        return $inertia->render($request, 'Auth/SignUp');
    }

    #[Route('/signup/{token}', name: 'auth.signup.confirm')]
    public function confirm(
        Request $request,
        string $token,
        SignUp\Confirm\ByToken\Handler $handler,
        LoginFormAuthenticator $authenticator,
        UserAuthenticatorInterface $userAuthenticator,
        InertiaService $inertia,
    ): Response {
        if (!$user = $this->users->findUserEntityBySignUpConfirmToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token.');

            return $inertia->redirect('/signup');
        }

        $command = new SignUp\Confirm\ByToken\Command($token);
        $userIdentity = $this->userProvider->loadUserByIdentifier($user->getEmail()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Вдало. Можна логінитися');

            return $userAuthenticator->authenticateUser(
                $userIdentity,
                $authenticator,
                $request
            );
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->redirect('/signup');
        }
    }
}
