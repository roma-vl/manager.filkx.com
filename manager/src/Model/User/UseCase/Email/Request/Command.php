<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Request;

use App\Validator\Constraints\UniqueUserEmail;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank(message: 'ID is required')]
    public string $id;

    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email format')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Email cannot be longer than {{ limit }} characters'
    )]
    #[UniqueUserEmail]
    public string $email;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
