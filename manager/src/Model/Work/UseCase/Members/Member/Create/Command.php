<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Members\Member\Create;

use App\Model\User\Entity\Account\Account;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $id;

    #[Assert\NotBlank]
    public string $group;

    #[Assert\NotBlank]
    public string $firstName;

    #[Assert\NotBlank]
    public string $lastName;

    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public Account $account;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
