<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank(message: 'Email is required.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    #[Assert\Length(
        max: 180,
        maxMessage: 'Email must not exceed {{ limit }} characters.'
    )]
    public string $email;

    #[Assert\NotBlank(message: 'First name is required.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'First name must be at least {{ limit }} characters.',
        maxMessage: 'First name must not exceed {{ limit }} characters.'
    )]
    public string $firstName;

    #[Assert\NotBlank(message: 'Last name is required.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Last name must be at least {{ limit }} characters.',
        maxMessage: 'Last name must not exceed {{ limit }} characters.'
    )]
    public string $lastName;
}
