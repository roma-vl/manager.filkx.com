<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\TakeAndStart;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public int $id;
    #[Assert\NotBlank]
    public string $actor;

    public function __construct(string $actor, int $id)
    {
        $this->id = $id;
        $this->actor = $actor;
    }
}
