<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role;

use App\Model\User\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank(message: 'User ID is required.')]
    public string $id;

    #[Assert\NotBlank(message: 'Role is required.')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Role must not exceed {{ limit }} characters.'
    )]
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
