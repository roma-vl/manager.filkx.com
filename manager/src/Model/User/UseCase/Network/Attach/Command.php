<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Attach;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank(message: 'User ID is required')]
    #[Assert\Length(
        max: 36,
        maxMessage: 'User ID cannot exceed {{ limit }} characters'
    )]
    public string $user;

    #[Assert\NotBlank(message: 'Network name is required')]
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

    public function __construct(string $user, string $network, string $identity)
    {
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;
    }
}
