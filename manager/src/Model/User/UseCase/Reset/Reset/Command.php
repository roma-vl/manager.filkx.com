<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Reset\Reset;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank(message: 'Reset token is required')]
    public string $token;

    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(
        min: 6,
        max: 100,
        minMessage: 'Password must be at least {{ limit }} characters long',
        maxMessage: 'Password cannot exceed {{ limit }} characters'
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/',
        message: 'Password must include at least one uppercase letter, one lowercase letter, and one number'
    )]
    public string $password;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}
