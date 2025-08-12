<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Files\Remove;

use App\Model\User\Entity\Account\Account;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $actor;
    #[Assert\NotBlank]
    public int $id;
    #[Assert\NotBlank]
    public string $file;

    #[Assert\NotBlank]
    public Account $account;

    public function __construct(string $actor, int $id, string $file, Account $account)
    {
        $this->actor = $actor;
        $this->id = $id;
        $this->file = $file;
        $this->account = $account;
    }
}
