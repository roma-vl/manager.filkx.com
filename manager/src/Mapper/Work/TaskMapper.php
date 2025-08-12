<?php

declare(strict_types=1);

namespace App\Mapper\Work;

use App\DTO\Work\Task\TaskDto;
use App\DTO\Work\Task\TaskExecutorDto;

class TaskMapper
{
    public static function map(array $task): TaskDto
    {
        $dto = new TaskDto();
        $dto->id = (string) $task['id'];
        $dto->name = $task['name'];
        $dto->project_id = $task['project_id'];
        $dto->project_name = $task['project_name'];
        $dto->author_id = $task['author_id'];
        $dto->author_name = $task['author_name'];
        $dto->status = $task['status'];
        $dto->priority = $task['priority'];
        $dto->progress = $task['progress'];
        $dto->date = $task['date'];
        $dto->plan_date = $task['plan_date'];
        $dto->parent = (string) $task['parent'] ?? null;
        $dto->type = $task['type'];
        $dto->root = (string) $task['parent'];

        foreach ($task['executors'] as $exec) {
            $executorDto = new TaskExecutorDto();
            $executorDto->task_id = (string) $exec['task_id'];
            $executorDto->name = $exec['name'];
            $dto->executors[] = $executorDto;
        }

        return $dto;
    }
}
