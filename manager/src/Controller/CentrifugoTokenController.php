<?php
namespace App\Controller;

use App\Security\UserIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Firebase\JWT\JWT;

class CentrifugoTokenController extends AbstractController
{
    public function __construct(
        private readonly Security $security
    ) {}

    #[Route('/api/centrifugo/token', name: 'centrifugo_token', methods: ['GET'])]
    public function token(): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof UserIdentity) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $hmacSecretKey = $_ENV['CENTRIFUGO_HMAC_SECRET'] ?? 'my_secret';

        $payload = [
            'sub' => (string)$user->getId(),
            'exp' => time() + 3600, // 1 година
            'channels' => ['chat'] // Дозволені канали
        ];

        $token = JWT::encode($payload, $hmacSecretKey, 'HS256');

        return $this->json([
            'token' => $token,
            'user' => $user->getUserIdentifier() // Для дебагу
        ]);
    }
}


