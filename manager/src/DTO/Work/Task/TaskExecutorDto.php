<?php
namespace App\DTO\Work\Task;

use Symfony\Component\Serializer\Annotation\Groups;

class TaskExecutorDto
{
    #[Groups(['task:list'])]
    public string $task_id;

    #[Groups(['task:list'])]
    public string $name;
}

