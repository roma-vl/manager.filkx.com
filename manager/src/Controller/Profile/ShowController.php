<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Infrastructure\Inertia\InertiaService;
use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShowController extends AbstractController
{
    public function __construct(
        private readonly UserFetcher $users,
    ) {}

    #[Route('/profile', name: 'profile')]
    public function show(Request $request, InertiaService $inertia): Response
    {
        $user = $this->users->get($this->getUser()->getId());

        return $inertia->render($request, 'Profile/Show', [
            'user' => [
                'id' => $user->getId()->getValue(),
                'first_name' => $user->getName()->getFirst(), // або ->getFirst().' '.$user->getLast()
                'last_name' => $user->getName()->getLast(), // або ->getFirst().' '.$user->getLast()
                'email' => $user->getEmail()->getValue(),
                'created_at' => $user->getDate()->format('Y-m-d H:i:s'),
                'roles' => $user->getRoles(),
                'status' => $user->getStatus(),
                'networks' => array_map(fn($n) => [
                    'network' => $n->getNetwork(),
                    'identity' => $n->getIdentity(),
                ], $user->getNetworks()),
            ],
        ]);

    }
}
