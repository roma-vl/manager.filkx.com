<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event)
    {
        $request = $event->getRequest();

        if ($request->headers->get('X-Inertia')) {
            $event->setResponse(new JsonResponse([], 409, [
                'X-Inertia-Location' => $request->getSchemeAndHttpHost() . '/login',
            ]));
        } else {
            $event->setResponse(new RedirectResponse('/login'));
        }
    }
}
