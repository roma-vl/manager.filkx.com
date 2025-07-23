<?php

declare(strict_types=1);

namespace App\Model\Comment\UseCase\Comment\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $author;
    #[Assert\NotBlank]
    public string $entityType;
    #[Assert\NotBlank]
    public string $entityId;
    #[Assert\NotBlank]
    public string $text;

    public function __construct(string $author, string $type, string $id)
    {
        $this->author = $author;
        $this->entityType = $type;
        $this->entityId = $id;
    }
}
