<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Confirm;
use Symfony\Component\Validator\Constraints as Assert;
class Command
{
    #[Assert\NotBlank(message: 'Confirmation token is required')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Token cannot exceed {{ limit }} characters'
    )]
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}
