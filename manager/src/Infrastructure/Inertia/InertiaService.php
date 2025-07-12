<?php

namespace App\Infrastructure\Inertia;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig\Environment as Twig;

class InertiaService
{
    private array $shared = [];
    private array $errors = [];

    public function __construct(
        private readonly Twig $twig,
    ) {}

    public function share(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    public function withErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    public function render(Request $request, string $component, array $props = []): Response
    {
        $props = array_merge($this->shared, $props);

        if (!empty($this->errors)) {
            $props['errors'] = $this->errors;
        }

        $page = [
            'component' => $component,
            'props' => $props,
            'url' => $request->getRequestUri(),
            'version' => null,
        ];

        if ($request->headers->get('X-Inertia')) {
            return new JsonResponse($page);
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

        if ($session?->getFlashBag()) {
            $this->share('flash', $session->getFlashBag()->all());
        }
    }
}
