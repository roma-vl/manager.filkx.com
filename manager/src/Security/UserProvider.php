<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User\Entity\Account\Account;
use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<UserIdentity>
 */
class UserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly UserFetcher $users,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class ' . $user::class);
        }

        return $this->loadUserByIdentifier($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return $class === UserIdentity::class
            || is_subclass_of($class, UserIdentity::class);
    }

    private function loadUser(string $username): AuthView
    {
        $chunks = explode(':', $username);

        if (\count($chunks) === 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }

        if ($user = $this->users->findForAuthByEmail($username)) {
            return $user;
        }

        throw new UserNotFoundException('User not found');
    }

    private function identityByUser(AuthView $user, string $username): UserIdentity
    {
        $account = null;

        if ($user->getAccountId()) {
            $account = $this->entityManager->getRepository(Account::class)
                ->find($user->getAccountId());
        }

        return new UserIdentity(
            $user->id,
            $user->email ?: $username,
            $user->password_hash ?: '',
            $user->name ?: $username,
            $user->role,
            $user->status,
            $user->date,
            $account
        );
    }

    public function loadUserByIdentifier(string $identifier): UserIdentity
    {
        $user = $this->loadUser($identifier);

        return $this->identityByUser($user, $identifier);
    }
}
