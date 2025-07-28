<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class HomeController extends BaseController
{
    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        return $this->json([
            'name' => 'JSON API',
        ]);
    }

    #[Route('/user', name: 'api_user')]
    public function user(): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->json([
            'email' => $this->getUser()->getUserIdentifier()
        ]);
    }

}
