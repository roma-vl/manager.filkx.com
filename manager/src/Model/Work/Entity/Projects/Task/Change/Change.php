<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\Change;

use App\Model\User\Entity\Account\Account;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Task\Task;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_projects_task_changes')]
class Change
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'changes')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Task $task;

    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_task_change_id')]
    private Id $id;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    public Account $account;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'actor_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Member $actor;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $date;

    #[ORM\Embedded(class: Set::class)]
    private Set $set;

    public function __construct(Task $task, Id $id, Member $actor, \DateTimeImmutable $date, Set $set, Account $account)
    {
        $this->task = $task;
        $this->id = $id;
        $this->actor = $actor;
        $this->date = $date;
        $this->set = $set;
        $this->account = $account;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getActor(): Member
    {
        return $this->actor;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getSet(): Set
    {
        return $this->set;
    }
}
