<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Validator\Constraints\UniqueUserEmail;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'First name must be at least {{ limit }} characters',
        maxMessage: 'First name cannot be longer than {{ limit }} characters'
    )]
    public string $firstName = '';

    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Last name must be at least {{ limit }} characters',
        maxMessage: 'Last name cannot be longer than {{ limit }} characters'
    )]
    public string $lastName = '';

    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email format')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Email cannot be longer than {{ limit }} characters'
    )]
    #[UniqueUserEmail]
    public string $email = '';

    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(
        min: 6,
        minMessage: 'Password must be at least {{ limit }} characters'
    )]
    public string $password = '';
}

