<?php

namespace App\ReadModel\Work\Projects\Task;

use App\Model\Work\Entity\Projects\Task\Status;
use App\Model\Work\Entity\Projects\Task\Type;
use App\ReadModel\Props\PropsProviderInterface;

final readonly class TaskMetaProvider implements PropsProviderInterface
{
    public function get(): array
    {
        return [
            'statuses' => [
                ['id' => Status::NEW, 'name' => 'NEW'],
                ['id' => Status::WORKING, 'name' => 'WORKING'],
                ['id' => Status::HELP, 'name' => 'HELP'],
                ['id' => Status::CHECKING, 'name' => 'CHECKING'],
                ['id' => Status::REJECTED, 'name' => 'REJECTED'],
                ['id' => Status::DONE, 'name' => 'DONE'],
            ],
            'types' => [
                ['id' => Type::NONE, 'name' => 'NONE'],
                ['id' => Type::ERROR, 'name' => 'ERROR'],
                ['id' => Type::FEATURE, 'name' => 'FEATURE'],
            ],
            'priorities' => [
                ['id' => 1, 'name' => 'LOW'],
                ['id' => 2, 'name' => 'NORMAL'],
                ['id' => 3, 'name' => 'FEATURE'],
                ['id' => 4, 'name' => 'HIGH'],
                ['id' => 5, 'name' => 'CRITICAL'],
                ['id' => 6, 'name' => 'BLOCKER'],
            ]
        ];
    }

    public function getProps(array $params = []): array
    {
        return $this->get();
    }
}
