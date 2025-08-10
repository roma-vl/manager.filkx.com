<?php

declare(strict_types=1);

namespace App\Controller\Api;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class HomeController extends AbstractController
{
    #[Route('/home', name: 'api.home', methods: ['GET'])]
    #[OA\Get(
        path: '/',
        summary: 'API Home',
        tags: ['API'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success response',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function home(): Response
    {
        return $this->json([
            'name' => 'JSON API',
        ]);
    }
}
