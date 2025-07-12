<?php

declare(strict_types=1);

namespace App\Infrastructure\Inertia;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment as Twig;

class InertiaService
{
    /** @var array<string, mixed> */
    private array $shared = [];

    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(
        private readonly Twig $twig,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function share(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    /**
     * @param array<string, string> $errors
     */
    public function withErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @param array<string, mixed> $props
     */
    public function render(Request $request, string $component, array $props = []): Response
    {
        if (!$request->isMethod('POST')) {
            $this->share('csrfToken', $this->csrfTokenManager->getToken('authenticate')->getValue());
        }

        $session = $request->getSession();
        if ($session instanceof Session && $session->getFlashBag()->has('error')) {
            $props['error'] = $session->getFlashBag()->get('error')[0];
        }

        $page = [
            'component' => $component,
            'props' => array_merge($this->shared, $props),
            'url' => $request->getRequestUri(),
            'version' => null,
        ];

        if ($request->headers->get('X-Inertia')) {
            return new JsonResponse($page, 200, [
                'Vary' => 'Accept',
                'X-Inertia' => 'true',
            ]);
        }

        return new Response(
            $this->twig->render('base.html.twig', ['page' => $page])
        );
    }

    public function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url, 303, ['X-Inertia' => 'true']);
    }

    public function location(string $url): JsonResponse
    {
        return new JsonResponse([], 409, ['X-Inertia-Location' => $url]);
    }

    public function shareFromSession(Request $request): void
    {
        $session = $request->getSession();
        if ($session instanceof Session) {
            $flashBag = $session->getFlashBag();
            if ($flashBag instanceof FlashBagInterface) {
                $this->share('flash', $flashBag->all());
            }
        }
    }
}
