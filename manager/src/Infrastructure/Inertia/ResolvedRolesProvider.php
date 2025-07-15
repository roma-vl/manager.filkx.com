<?php

declare(strict_types=1);

namespace App\Infrastructure\Inertia;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class ResolvedRolesProvider
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    public function getResolvedRoles(): array
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return [];
        }

        return $this->roleHierarchy->getReachableRoleNames($token->getRoleNames());
    }
}
