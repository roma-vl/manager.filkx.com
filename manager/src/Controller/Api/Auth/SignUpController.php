<?php

declare(strict_types=1);

namespace App\Controller\Api\Auth;

use App\Model\User\UseCase\SignUp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth')]
final class SignUpController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {}

    #[OA\Post(
        path: '/auth/signup',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['first_name', 'last_name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'first_name', type: 'string'),
                    new OA\Property(property: 'last_name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created'),
            new OA\Response(
                response: 400,
                description: 'Validation errors',
                content: new OA\JsonContent(ref: '#/components/schemas/ViolationList')
            ),
        ]
    )]

    #[Route('/auth/signup', name: 'api.auth.signup', methods: ['POST'])]
    public function __invoke(Request $request, SignUp\Request\Handler $handler): JsonResponse
    {
        /** @var SignUp\Request\Command $command */
        $command = $this->serializer->deserialize($request->getContent(), SignUp\Request\Command::class, 'json');

        $violations = $this->validator->validate($command);
        if (\count($violations) > 0) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return new JsonResponse([], JsonResponse::HTTP_CREATED);
    }
}
