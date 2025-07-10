<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\ErrorHandler;
use App\Model\User\UseCase\SignUp;
use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SignUpController extends AbstractController
{
    public function __construct(
        private readonly UserFetcher $users,
        private readonly ErrorHandler $errors,
    ) {}

    #[Route('/signup', name: 'auth.signup')]
    public function request(Request $request, SignUp\Request\Handler $handler): Response
    {
        $command = new SignUp\Request\Command();
        $form = $this->createForm(SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('home');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/signup/{token}', name: 'auth.signup.confirm')]
    public function confirm(
        Request $request,
        string $token,
        SignUp\Confirm\ByToken\Handler $handler,
        LoginFormAuthenticator $authenticator,
        UserAuthenticatorInterface $userAuthenticator,
    ): Response {
        if (!$user = $this->users->findUserEntityBySignUpConfirmToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token.');
            return $this->redirectToRoute('auth.signup');
        }

        $command = new SignUp\Confirm\ByToken\Command($token);

        try {
            $handler->handle($command);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('auth.signup');
        }
    }
}
