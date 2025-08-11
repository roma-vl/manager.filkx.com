<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Create;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Project\Id as ProjectId;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use App\Model\Work\Entity\Projects\Task\Type;
use Doctrine\DBAL\Exception;

class Handler
{
    private MemberRepository $members;
    private ProjectRepository $projects;
    private TaskRepository $tasks;
    private Flusher $flusher;

    public function __construct(
        MemberRepository $members,
        ProjectRepository $projects,
        TaskRepository $tasks,
        Flusher $flusher
    )
    {
        $this->members = $members;
        $this->projects = $projects;
        $this->tasks = $tasks;
        $this->flusher = $flusher;
    }

    /**
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $member = $this->members->get(new MemberId($command->member));
        $project = $this->projects->get(new ProjectId($command->project));

        $parent = $command->parent ? $this->tasks->get(new Id($command->parent)) : null;

        $date = new \DateTimeImmutable();

        $tasks = [];

        foreach ($command->names as $name) {
            $nameText = \is_object($name) && property_exists($name, 'name') ? $name->name : (string) $name;

            $task = new Task(
                $this->tasks->nextId(),
                $project,
                $member,
                $date,
                new Type($command->type),
                $command->priority,
                $nameText,
                $command->content,
                $command->account,
            );

            if ($parent) {
                $task->setChildOf($member, $date, $parent, $command->account);
            }

            if ($command->plan) {
                $task->plan($member, $date, $command->plan, $command->account);
            }

            $this->tasks->add($task);

            $tasks[] = $task;
        }

        $this->flusher->flush(...$tasks);
    }
}
