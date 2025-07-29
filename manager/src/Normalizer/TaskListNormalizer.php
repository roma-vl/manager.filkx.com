<?php

namespace App\Normalizer;

use App\Mapper\Work\TaskMapper;
use Symfony\Component\Serializer\SerializerInterface;

class TaskListNormalizer
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {}

    public function normalize(array $tasks): array
    {
        $mapped = array_map(fn($task) => TaskMapper::map($task), $tasks);

        return $this->serializer->normalize($mapped, null, ['groups' => ['task:list']]);
    }
}
