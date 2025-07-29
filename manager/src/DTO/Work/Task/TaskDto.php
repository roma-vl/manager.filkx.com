<?php

namespace App\DTO\Work\Task;

use Symfony\Component\Serializer\Annotation\Groups;

class TaskDto
{
    #[Groups(['task:list'])]
    public string $id;

    #[Groups(['task:list'])]
    public string $name;

    #[Groups(['task:list'])]
    public string $project_id;

    #[Groups(['task:list'])]
    public string $project_name;

    #[Groups(['task:list'])]
    public string $author_id;

    #[Groups(['task:list'])]
    public string $author_name;

    #[Groups(['task:list'])]
    public string $status;

    #[Groups(['task:list'])]
    public int $priority;

    #[Groups(['task:list'])]
    public int $progress;

    #[Groups(['task:list'])]
    public string $date;

    #[Groups(['task:list'])]
    public ?string $plan_date;

    /** @var TaskExecutorDto[] */
    #[Groups(['task:list'])]
    public array $executors = [];

    #[Groups(['task:list'])]
    public ?string $parent = null;

    #[Groups(['task:list'])]
    public string $type;

    #[Groups(['task:list'])]
    public ?string $root = null;
}

