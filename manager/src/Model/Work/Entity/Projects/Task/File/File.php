<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task\File;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Task\Task;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_projects_task_files')]
#[ORM\Index(columns: ['date'])]
final class File
{
    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_task_file_id')]
    private Id $id;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'files')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: false)]
    private Task $task;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', nullable: false)]
    private Member $member;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $date;

    #[ORM\Embedded(class: Info::class)]
    private Info $info;

    public function __construct(Task $task, Id $id, Member $member, \DateTimeImmutable $date, Info $info)
    {
        $this->task = $task;
        $this->id = $id;
        $this->member = $member;
        $this->date = $date;
        $this->info = $info;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getInfo(): Info
    {
        return $this->info;
    }

    public function getTask(): Task
    {
        return $this->task;
    }
}
