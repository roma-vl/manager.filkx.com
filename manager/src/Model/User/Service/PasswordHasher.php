<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

readonly class PasswordHasher
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function hash(PasswordAuthenticatedUserInterface $user, string $password): string
    {
        return $this->hasher->hashPassword($user, $password);
    }

    public function validate(PasswordAuthenticatedUserInterface $user, string $password): bool
    {
        return $this->hasher->isPasswordValid($user, $password);
    }
}
