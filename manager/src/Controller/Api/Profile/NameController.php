<?php

declare(strict_types=1);

namespace App\Controller\Api\Profile;

use App\Model\User\UseCase\Name;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Put(
    path: '/api/profile/name',
    summary: 'Update user name',
    security: [['oauth2' => ['common']]],
    requestBody: new OA\RequestBody(
        content: new OA\JsonContent(
            required: ['first', 'last'],
            properties: [
                new OA\Property(property: 'first', type: 'string', example: 'Ivan'),
                new OA\Property(property: 'last', type: 'string', example: 'Petrenko'),
            ],
            type: 'object'
        )
    ),
    tags: ['Profile'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Name updated successfully'
        ),
        new OA\Response(
            response: 400,
            description: 'Validation errors',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorModel')
        )
    ]
)]
#[Route('/api/profile/name', name: 'api.profile.name', methods: ['PUT'])]
class NameController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    public function __invoke(Request $request, Name\Handler $handler): Response
    {
        /** @var Name\Command $command */
        $command = $this->serializer->deserialize(
            $request->getContent(),
            Name\Command::class,
            'json',
            [
                'object_to_populate' => new Name\Command($this->getUser()->getId()),
                'ignored_attributes' => ['id'],
            ]
        );

        $violations = $this->validator->validate($command);
        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, 400, [], true);
        }

        $handler->handle($command);

        return $this->json([], 200);
    }
}
