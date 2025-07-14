<?php

declare(strict_types=1);

namespace App\Infrastructure\Inertia;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsEventListener(event: 'kernel.controller_arguments')]

readonly class InertiaRequestListener
{
    public function __construct(
        private InertiaService        $inertia,
        private TokenStorageInterface $tokenStorage,
        private RequestStack          $requestStack,
        private ResolvedRolesProvider $rolesProvider,
    ) {
    }


    public function __invoke(ControllerArgumentsEvent $event): void
    {
        $request = $event->getRequest();

        // 🔐 Додаємо користувача
        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        if ($user instanceof UserInterface) {
            $userData = [
                'id' => method_exists($user, 'getId') ? (string) $user->getId() : null,
                'email' => $user->getUserIdentifier(),
                'name' => method_exists($user, 'getDisplay') ? $user->getDisplay() : null,
                'roles' => $user->getRoles(),
                'status' => method_exists($user, 'getStatus') ? $user->getStatus() : null,
                'created_at' => method_exists($user, 'getDate') ? $user->getDate() : null,
            ];

            $this->inertia->share('auth', [
                'user' => $userData,
                'roles' => $this->rolesProvider->getResolvedRoles(),
            ]);
        } else {
            $this->inertia->share('auth', ['user' => null, 'permissions' => []]);
        }

        // 💬 Додаємо flash-повідомлення
        $session = $this->requestStack->getSession();
        if ($session && $session->getFlashBag()) {
            $flashBag = $session->getFlashBag();
            $this->inertia->share('flash', [
                'success' => $flashBag->has('success') ? $flashBag->get('success')[0] : null,
                'error' => $flashBag->has('error') ? $flashBag->get('error')[0] : null,
                'info' => $flashBag->has('info') ? $flashBag->get('info')[0] : null,
            ]);
        }
    }
}
