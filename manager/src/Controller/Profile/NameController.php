<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\BaseController;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\EntityNotFoundException;
use App\Model\User\UseCase\Name;
use App\ReadModel\Props\ProfilePropsProvider;
use App\Service\CommandFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class NameController extends BaseController
{
    public function __construct(
        private readonly ProfilePropsProvider $userPropsProvider,
    ) {
    }

    #[Route('/profile/name', name: 'profile.name.update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function request(
        Request $request,
        InertiaService $inertia,
        Name\Handler $handler,
        CommandFactory $commandFactory,
    ): Response {
        $userId = $this->getUser()->getId();
        $command = new Name\Command((string) $userId);
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
            $this->addFlash('success', 'Name changed.');

            return $inertia->redirect('/profile');
        } catch (EntityNotFoundException|\DomainException $e) {
            return $inertia->render($request, 'Profile/Show', [
                'errors' => ['email' => $e->getMessage()],
            ]);
        }
    }
}
