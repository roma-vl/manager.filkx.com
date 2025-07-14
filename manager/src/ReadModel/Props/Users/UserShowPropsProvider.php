<?php

declare(strict_types=1);

namespace App\ReadModel\Props\Users;

use App\ReadModel\Props\PropsProviderInterface;
use App\ReadModel\User\UserFetcher;

final readonly class UserShowPropsProvider implements PropsProviderInterface
{
    public function __construct(
        private UserFetcher $userFetcher,
    ) {
    }

    public function getProps(array $params = []): array
    {
        $userId = $params['userId'] ?? null;
        if (!$userId) {
            throw new \InvalidArgumentException('User ID is required in params.');
        }

        $user = $this->userFetcher->get($userId);

        return [
            'user' => [
                'id' => $user->getId()->getValue(),
                'name' => [
                    'first' => $user->getName()->getFirst(),
                    'last' => $user->getName()->getLast(),
                    'full' => $user->getName()->getFull(),
                ],
                'email' => $user->getEmail()?->getValue(),
                'date' => $user->getDate()->format('Y-m-d H:i:s'),
                'role' => $user->getRole()->getName(),
                'status' => $user->getStatus(),
                'networks' => array_map(fn($n) => [
                    'network' => $n->getNetwork(),
                    'identity' => $n->getIdentity(),
                ], $user->getNetworks()),
                'wait' => $user->isWait(),
                'active' => $user->isActive(),
                'blocked' => $user->isBlocked(),
            ],
        ];
    }
}
