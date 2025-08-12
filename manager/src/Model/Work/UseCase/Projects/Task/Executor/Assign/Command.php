<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Executor\Assign;

use App\Model\User\Entity\Account\Account;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $actor;
    #[Assert\NotBlank]
    public int $id;
    #[Assert\NotBlank]
    public array $members;

    #[Assert\NotBlank]
    public Account $account;

    public function __construct(string $actor, int $id, Account $account)
    {
        $this->actor = $actor;
        $this->id = $id;
        $this->account = $account;
    }
}
