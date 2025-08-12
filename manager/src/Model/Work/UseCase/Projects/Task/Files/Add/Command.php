<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Files\Add;

use App\Model\User\Entity\Account\Account;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $actor;
    #[Assert\NotBlank]
    public int $id;


    #[Assert\NotBlank]
    public Account $account;

    public array $files;

    public function __construct(string $actor, int $id, Account $account)
    {
        $this->id = $id;
        $this->actor = $actor;
        $this->account = $account;
    }
}
