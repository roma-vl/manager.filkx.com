<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank(message: 'Network is required')]
    #[Assert\Length(
        max: 16,
        maxMessage: 'Network name cannot exceed {{ limit }} characters'
    )]
    public string $network;

    #[Assert\NotBlank(message: 'Identity is required')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Identity cannot exceed {{ limit }} characters'
    )]
    public string $identity;

    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'First name cannot exceed {{ limit }} characters'
    )]
    public string $firstName;

    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Last name cannot exceed {{ limit }} characters'
    )]
    public string $lastName;

    public function __construct(string $network, string $identity)
    {
        $this->network = $network;
        $this->identity = $identity;
    }
}
