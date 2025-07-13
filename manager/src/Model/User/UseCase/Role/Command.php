<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role;

use App\Model\User\Entity\User\User;

class Command
{
    public string $id;

    public string $role;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromUser(User $user): self
    {
        $command = new self($user->getId()->getValue());
        $command->role = $user->getRole()->getName();

        return $command;
    }
}
