<?php

namespace App\Builder\Work\Task;

use App\DTO\Work\Task\TaskMetaView;
use App\ReadModel\Work\Projects\Task\TaskMetaProvider;

class TaskMetaBuilder
{
    public function __construct(
        private readonly TaskMetaProvider $provider,
    ) {}

    public function build(): TaskMetaView
    {
        $props = $this->provider->get();

        $meta = new TaskMetaView();
        $meta->statuses = $props['statuses'];
        $meta->types = $props['types'];
        $meta->priorities = $props['priorities'];

        return $meta;
    }
}

