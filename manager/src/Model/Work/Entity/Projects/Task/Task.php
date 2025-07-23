<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Task\Change\Change;
use App\Model\Work\Entity\Projects\Task\Change\Id as ChangeId;
use App\Model\Work\Entity\Projects\Task\Change\Set;
use App\Model\Work\Entity\Projects\Task\File\File;
use App\Model\Work\Entity\Projects\Task\File\Id as FileId;
use App\Model\Work\Entity\Projects\Task\File\Info;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;


#[ORM\Entity]
#[ORM\Table(name: 'work_projects_tasks')]
#[ORM\Index(columns: ['date'])]
class Task implements AggregateRoot
{
    use EventsTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_task_id')]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'work_projects_tasks_seq')]
    private Id $id;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false)]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false)]
    private Member $author;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $date;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $planDate = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content = null;

    /** @var Collection<int, File> */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'task', cascade: ['all'], orphanRemoval: true)]
    #[ORM\OrderBy(['date' => 'ASC'])]
    private Collection $files;

    #[ORM\Column(type: 'work_projects_task_type', length: 16)]
    private Type $type;

    #[ORM\Column(type: 'smallint')]
    private int $progress = 0;

    #[ORM\Column(type: 'smallint')]
    private int $priority;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?self $parent = null;

    #[ORM\Column(type: 'work_projects_task_status', length: 16)]
    private Status $status;


    #[ORM\ManyToMany(targetEntity: Member::class)]
    #[ORM\JoinTable(name: 'work_projects_tasks_executors')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'member_id', referencedColumnName: 'id')]
    #[ORM\OrderBy(['name.first' => 'ASC'])]
    private Collection $executors;


    #[ORM\OneToMany(targetEntity: Change::class, mappedBy: 'task', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $changes;

    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    private int $version;

    public function __construct(
        Id $id,
        Project $project,
        Member $author,
        \DateTimeImmutable $date,
        Type $type,
        int $priority,
        string $name,
        ?string $content
    ) {
        $this->id = $id;
        $this->project = $project;
        $this->author = $author;
        $this->date = $date;
        $this->name = $name;
        $this->content = $content;
        $this->files = new ArrayCollection();
        $this->type = $type;
        $this->priority = $priority;
        $this->status = Status::new();
        $this->executors = new ArrayCollection();
        $this->changes = new ArrayCollection();
        $this->addChange($author, $date, Set::forNewTask($project->getId(), $name, $content, $type, $priority));
    }

    public function edit(Member $actor, \DateTimeImmutable $date, string $name, ?string $content): void
    {
        if ($name !== $this->name) {
            $this->name = $name;
            $this->addChange($actor, $date, Set::fromName($name));
        }
        if ($content !== $this->content) {
            $this->content = $content;
            $this->addChange($actor, $date, Set::fromContent($content));
        }
        $this->recordEvent(new Event\TaskEdited($actor->getId(), $this->id, $name, $content));
    }

    public function addFile(Member $actor, \DateTimeImmutable $date, FileId $id, Info $info): void
    {
        $this->files->add(new File($this, $id, $actor, $date, $info));
        $this->addChange($actor, $date, Set::fromFile($id));
        $this->recordEvent(new Event\TaskFileAdded($actor->getId(), $this->id, $id, $info));
    }

    public function removeFile(Member $actor, \DateTimeImmutable $date, FileId $id): void
    {
        foreach ($this->files as $current) {
            if ($current->getId()->isEqual($id)) {
                $this->files->removeElement($current);
                $this->addChange($actor, $date, Set::fromRemovedFile($current->getId()));
                $this->recordEvent(new Event\TaskFileRemoved($actor->getId(), $this->id, $id, $current->getInfo()));
                return;
            }
        }
        throw new \DomainException('File is not found.');
    }

    public function start(Member $actor, \DateTimeImmutable $date): void
    {
        if (!$this->isNew()) {
            throw new \DomainException('Task is already started.');
        }
        if (!$this->executors->count()) {
            throw new \DomainException('Task does not contain executors.');
        }
        $this->changeStatus($actor, $date, Status::working());
    }

    public function setChildOf(Member $actor, \DateTimeImmutable $date, Task $parent): void
    {
        if ($parent === $this->parent) {
            return;
        }

        $current = $parent;
        do {
            if ($current === $this) {
                throw new \DomainException('Cyclomatic children.');
            }
        }
        while ($current && $current = $current->getParent());

        $this->parent = $parent;

        $this->addChange($actor, $date, Set::fromParent($parent->getId()));
    }

    public function setRoot(Member $actor, \DateTimeImmutable $date): void
    {
        $this->parent = null;
        $this->addChange($actor, $date, Set::forRemovedParent());
    }

    public function plan(Member $actor, \DateTimeImmutable $date, \DateTimeImmutable $plan): void
    {
        $this->planDate = $plan;
        $this->addChange($actor, $date, Set::fromPlan($plan));
        $this->recordEvent(new Event\TaskPlanChanged($actor->getId(), $this->id, $date));
    }

    public function removePlan(Member $actor, \DateTimeImmutable $date): void
    {
        $this->planDate = null;
        $this->addChange($actor, $date, Set::forRemovedPlan());
        $this->recordEvent(new Event\TaskPlanChanged($actor->getId(), $this->id, null));
    }

    public function move(Member $actor, \DateTimeImmutable $date, Project $project): void
    {
        if ($project === $this->project) {
            throw new \DomainException('Project is already same.');
        }
        $this->project = $project;
        $this->addChange($actor, $date, Set::fromProject($project->getId()));
    }

    public function changeType(Member $actor, \DateTimeImmutable $date, Type $type): void
    {
        if ($this->type->isEqual($type)) {
            throw new \DomainException('Type is already same.');
        }
        $this->type = $type;
        $this->addChange($actor, $date, Set::fromType($type));
        $this->recordEvent(new Event\TaskTypeChanged($actor->getId(), $this->id, $type));
    }

    public function changeStatus(Member $actor, \DateTimeImmutable $date, Status $status): void
    {
        if ($this->status->isEqual($status)) {
            throw new \DomainException('Status is already same.');
        }
        $this->status = $status;
        $this->addChange($actor, $date, Set::fromStatus($status));
        $this->recordEvent(new Event\TaskStatusChanged($actor->getId(), $this->id, $status));
        if (!$status->isNew() && !$this->startDate) {
            $this->startDate = $date;
        }
        if ($status->isDone()) {
            if ($this->progress !== 100) {
                $this->changeProgress($actor, $date, 100);
            }
            $this->endDate = $date;
        } else {
            $this->endDate = null;
        }
    }

    public function changeProgress(Member $actor, \DateTimeImmutable $date, int $progress): void
    {
        Assert::range($progress, 0, 100);
        if ($progress === $this->progress) {
            throw new \DomainException('Progress is already same.');
        }
        $this->progress = $progress;
        $this->addChange($actor, $date, Set::fromProgress($progress));
        $this->recordEvent(new Event\TaskProgressChanged($actor->getId(), $this->id, $progress));
    }

    public function changePriority(Member $actor, \DateTimeImmutable $date, int $priority): void
    {
        Assert::range($priority, 1, 6);
        if ($priority === $this->priority) {
            throw new \DomainException('Priority is already same.');
        }
        $this->priority = $priority;
        $this->addChange($actor, $date, Set::fromPriority($priority));
        $this->recordEvent(new Event\TaskPriorityChanged($actor->getId(), $this->id, $priority));
    }

    public function hasExecutor(MemberId $id): bool
    {
        foreach ($this->executors as $executor) {
            if ($executor->getId()->isEqual($id)) {
                return true;
            }
        }
        return false;
    }

    public function assignExecutor(Member $actor, \DateTimeImmutable $date, Member $executor): void
    {
        if ($this->executors->contains($executor)) {
            throw new \DomainException('Executor is already assigned.');
        }
        $this->executors->add($executor);
        $this->addChange($actor, $date, Set::fromExecutor($executor->getId()));
        $this->recordEvent(new Event\TaskExecutorAssigned($actor->getId(), $this->id, $executor->getId()));
    }

    public function revokeExecutor(Member $actor, \DateTimeImmutable $date, MemberId $id): void
    {
        foreach ($this->executors as $current) {
            if ($current->getId()->isEqual($id)) {
                $this->executors->removeElement($current);
                $this->addChange($actor, $date, Set::fromRevokedExecutor($current->getId()));
                $this->recordEvent(new Event\TaskExecutorRevoked($actor->getId(), $this->id, $current->getId()));
                return;
            }
        }
        throw new \DomainException('Executor is not assigned.');
    }

    public function isNew(): bool
    {
        return $this->status->isNew();
    }

    public function isWorking(): bool
    {
        return $this->status->isWorking();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getPlanDate(): ?\DateTimeImmutable
    {
        return $this->planDate;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files->toArray();
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getParent(): ?Task
    {
        return $this->parent;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return Member[]
     */
    public function getExecutors(): array
    {
        return $this->executors->toArray();
    }

    /**
     * @return Change[]
     */
    public function getChanges(): array
    {
        return $this->changes->toArray();
    }

    private function addChange(Member $actor, \DateTimeImmutable $date, Set $set): void
    {
        if ($last = $this->changes->last()) {
            /** @var Change $last */
            $next = $last->getId()->next();
        } else {
            $next = ChangeId::first();
        }
        $this->changes->add(new Change($this, $next, $actor, $date, $set));
    }
}
