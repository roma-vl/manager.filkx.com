<?php

declare(strict_types=1);

namespace App\Controller\Api\Profile;

use App\Model\User\Entity\User\Network;
use App\ReadModel\User\UserFetcher;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Get(
    path: '/api/profile',
    summary: 'Get current authenticated user profile',
    security: [['oauth2' => ['common']]],
    tags: ['Profile'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Success response',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 123),
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(
                        property: 'name',
                        properties: [
                            new OA\Property(property: 'first', type: 'string', example: 'Іван'),
                            new OA\Property(property: 'last', type: 'string', example: 'Шевченко'),
                        ],
                        type: 'object'
                    ),
                    new OA\Property(
                        property: 'networks',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'name', type: 'string', example: 'google'),
                                new OA\Property(property: 'identity', type: 'string', example: '123456789'),
                            ],
                            type: 'object'
                        )
                    ),
                ],
                type: 'object'
            )
        )
    ]
)]
#[Route('/api/profile', name: 'api.profile.show', methods: ['GET'])]
class ShowController extends AbstractController
{
    public function __invoke(UserFetcher $users): Response
    {
        $user = $users->get($this->getUser()->getId());

        return $this->json([
            'id' => $user->getId()->getValue(),
            'email' => $user->getEmail()?->getValue(),
            'name' => [
                'first' => $user->getName()->getFirst(),
                'last' => $user->getName()->getLast(),
            ],
            'networks' => array_map(static function (Network $network): array {
                return [
                    'name' => $network->getNetwork(),
                    'identity' => $network->getIdentity(),
                ];
            }, $user->getNetworks())
        ]);
    }
}
