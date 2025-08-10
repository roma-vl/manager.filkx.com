<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User\Entity\Account\Account;
use App\Model\User\Entity\User\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, EquatableInterface, PasswordAuthenticatedUserInterface
{
    private string $id;
    private string $username;
    private string $password;
    private string $display;
    private string $role;
    private string $status;
    private string $date;

    private ?Account $account;

    public function __construct(
        string $id,
        string $username,
        string $password,
        string $display,
        string $role,
        string $status,
        string $date,
        ?Account $account = null,
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->display = $display;
        $this->role = $role;
        $this->status = $status;
        $this->date = $date;
        $this->account = $account;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function isActive(): bool
    {
        return $this->status === User::STATUS_ACTIVE;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return
            $this->id === $user->id
            && $this->password === $user->password
            && $this->role === $user->role
            && $this->status === $user->status;
    }

    public function getUserIdentifier(): string
    {
        if ($this->username === '') {
            throw new \RuntimeException('Username cannot be empty');
        }

        return $this->username;
    }
}
