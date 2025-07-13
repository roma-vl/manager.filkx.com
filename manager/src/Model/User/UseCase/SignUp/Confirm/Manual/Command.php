<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Confirm\Manual;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank(message: 'User ID is required')]
    #[Assert\Length(
        max: 36,
        maxMessage: 'User ID cannot exceed {{ limit }} characters'
    )]
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
