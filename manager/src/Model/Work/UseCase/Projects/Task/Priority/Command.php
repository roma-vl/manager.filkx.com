<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Priority;

use App\Model\User\Entity\Account\Account;
use App\Model\Work\Entity\Projects\Task\Task;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $actor;
    #[Assert\NotBlank]
    public int $id;
    #[Assert\NotBlank]
    public $priority;

    #[Assert\NotBlank]
    public Account $account;

    public function __construct(string $actor, int $id, Account $account)
    {
        $this->actor = $actor;
        $this->id = $id;
        $this->account = $account;
    }

    public static function fromTask(string $actor, Task $task, Account $account): self
    {
        $command = new self($actor, $task->getId()->getValue(), $account);
        $command->priority = $task->getPriority();

        return $command;
    }
}
