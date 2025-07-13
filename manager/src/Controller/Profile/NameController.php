<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\BaseController;
use App\Infrastructure\Inertia\InertiaService;
use App\Model\EntityNotFoundException;
use App\Model\User\UseCase\Name;
use App\ReadModel\User\UserFetcher;
use App\Service\CommandFactory;
use DomainException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class NameController extends BaseController
{
    public function __construct(
        private readonly UserFetcher $users,
    ){}

    #[Route('/profile/name', name: 'profile.name.update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function request(
        Request        $request,
        InertiaService $inertia,
        Name\Handler   $handler,
        CommandFactory $commandFactory
    ): Response
    {

        $userId = $this->getUser()->getId();
        $command = new Name\Command((string)$userId);
        $errors = $commandFactory->createFromRequest($request, $command);

        if ($errors) {
            $user = $this->users->get($this->getUser()->getId());
            $userProps = [
                'user' => [
                    'id' => $user->getId()->getValue(),
                    'first_name' => $user->getName()->getFirst(),
                    'last_name' => $user->getName()->getLast(),
                    'email' => $user->getEmail()->getValue(),
                    'created_at' => $user->getDate()->format('Y-m-d H:i:s'),
                    'roles' => $user->getRoles(),
                    'status' => $user->getStatus(),
                    'networks' => array_map(fn($n) => [
                        'network' => $n->getNetwork(),
                        'identity' => $n->getIdentity(),
                    ], $user->getNetworks()),
                ],
            ];

            return $this->renderWithErrors($request, $inertia, 'Profile/Show', $errors, $userProps);
        }


        try {
            $handler->handle($command);
            $this->addFlash('success', 'Name changed.');
            return $inertia->redirect('/profile');
        } catch (EntityNotFoundException|DomainException $e) {
            return $inertia->render($request, 'Profile/Show', [
                'errors' => ['email' => $e->getMessage()],
            ]);
        }


    }
}
