<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\BaseController;
use App\Controller\ErrorHandler;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\EntityNotFoundException;
use App\Model\User\UseCase\Email;
use App\ReadModel\Props\ProfilePropsProvider;
use App\Service\CommandFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class EmailController extends BaseController
{
    public function __construct(
        private readonly ErrorHandler         $errors,
        private readonly ProfilePropsProvider $userPropsProvider,
    ) {
    }

    #[Route('/profile/email', name: 'profile.email.update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function request(
        Request $request,
        InertiaService $inertia,
        Email\Request\Handler $handler,
        CommandFactory $commandFactory,
    ): Response {
        $userId = $this->getUser()->getId();
        $command = new Email\Request\Command((string) $userId);
        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            return $this->renderWithErrors(
                $request,
                $inertia,
                'Profile/Show',
                $errors,
                $this->userPropsProvider->getProps(['userId' => $this->getUser()->getId()])
            );
        }

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Check your email.');

            return $inertia->redirect('/profile');
        } catch (EntityNotFoundException|\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());

            return $inertia->redirect('/profile');
        }
    }

    #[Route('/profile/email/{token}', name: 'profile.email.confirm', methods: ['GET'])]
    public function confirm(string $token, Email\Confirm\Handler $handler): Response
    {
        $command = new Email\Confirm\Command($this->getUser()->getId(), $token);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Email is successfully changed.');
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('profile');
    }
}
