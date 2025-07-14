<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Infrastructure\Inertia\InertiaService;
use App\ReadModel\Props\ProfilePropsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShowController extends AbstractController
{
    public function __construct(
        private readonly ProfilePropsProvider $userPropsProvider,
    ) {
    }

    #[Route('/profile', name: 'profile')]
    public function show(Request $request, InertiaService $inertia): Response
    {
        return $inertia->render($request, 'Profile/Show',
            $this->userPropsProvider->getProps([
                'userId' => $this->getUser()->getId(),
            ])
        );
    }
}
