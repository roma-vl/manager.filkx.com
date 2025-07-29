<?php

namespace App\DTO\Work\Task;

use Symfony\Component\Serializer\Annotation\Groups;

final class TaskMetaView
{
    #[Groups(['task:list'])]
    public array $statuses = [];

    #[Groups(['task:list'])]
    public array $types = [];

    #[Groups(['task:list'])]
    public array $priorities = [];

    #[Groups(['task:list'])]
    public array $progress = [];
}
