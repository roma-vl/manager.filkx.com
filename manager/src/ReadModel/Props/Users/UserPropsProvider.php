<?php

declare(strict_types=1);

namespace App\ReadModel\Props\Users;

use App\ReadModel\Props\PropsProviderInterface;
use App\ReadModel\User\UserFetcher;

final readonly class UserPropsProvider implements PropsProviderInterface
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
                'email' => $user->getEmail()?->getValue(),
                'firstName' => $user->getName()?->getFirst(),
                'lastName' => $user->getName()?->getLast(),
                'roles' => $user->getRoles(),
            ],
        ];
    }
}
