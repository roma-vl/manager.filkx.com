<?php

declare(strict_types=1);

namespace App\ReadModel\Props;

use App\ReadModel\User\UserFetcher;

final readonly class ProfilePropsProvider implements PropsProviderInterface
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
                'first_name' => $user->getName()->getFirst(),
                'last_name' => $user->getName()->getLast(),
                'email' => $user->getEmail()->getValue(),
                'created_at' => $user->getDate()->format('Y-m-d H:i:s'),
                'roles' => $user->getRoles(),
                'status' => $user->getStatus(),
                'networks' => array_map(fn ($n) => [
                    'network' => $n->getNetwork(),
                    'identity' => $n->getIdentity(),
                ], $user->getNetworks()),
            ],
        ];
    }
}
