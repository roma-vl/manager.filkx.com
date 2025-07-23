<?php

declare(strict_types=1);

namespace App\Service\Work\Processor\Driver;

use App\ReadModel\Work\Projects\Task\TaskFetcher;

class TaskDriver implements Driver
{
    private const PATTERN = '/\#\d+/';

    private TaskFetcher $tasks;

    public function __construct(TaskFetcher $tasks)
    {
        $this->tasks = $tasks;
    }

    public function process(string $text): string
    {
        return preg_replace_callback(self::PATTERN, function (array $matches) {
            $id = ltrim($matches[0], '#');
            if (!$task = $this->tasks->find($id)) {
                return $matches[0];
            }

            return sprintf(
                '<a href="/work/projects/tasks/%s" class="text-green-600 hover:underline">%s</a>',
                $task->getId()->getValue(),
                htmlspecialchars($task->getName())
            );

        }, $text);
    }

}
