<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Role\Create;

use App\Model\User\Entity\Account\Account;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    public Account $account;


    public array $permissions;
}
