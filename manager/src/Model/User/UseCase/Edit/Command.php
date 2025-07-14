<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Edit;

use App\Model\User\Entity\User\User;
use App\Validator\Constraints\UniqueUserEmail;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $id;

    #[Assert\NotBlank(message: 'Email is required.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    #[Assert\Length(
        max: 180,
        maxMessage: 'Email must not exceed {{ limit }} characters.'
    )]
    #[UniqueUserEmail]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "First name must be at least {{ limit }} characters long",
        maxMessage: "First name cannot be longer than {{ limit }} characters"
    )]
    public string $firstName;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Last name must be at least {{ limit }} characters long",
        maxMessage: "Last name cannot be longer than {{ limit }} characters"
    )]
    public string $lastName;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromUser(User $user): self
    {
        $command = new self($user->getId()->getValue());
        $command->email = $user->getEmail()?->getValue();
        $command->firstName = $user->getName()->getFirst();
        $command->lastName = $user->getName()->getLast();

        return $command;
    }
}
