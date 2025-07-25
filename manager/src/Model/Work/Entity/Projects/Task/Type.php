<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use Webmozart\Assert\Assert;

class Type
{
    public const NONE = 'none';
    public const ERROR = 'error';
    public const BUG = 'bug';
    public const FEATURE = 'feature';
    public const TASK = 'task';
    public const SUPPORT = 'support';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::NONE,
            self::ERROR,
            self::BUG,
            self::FEATURE,
            self::TASK,
            self::SUPPORT,
        ]);

        $this->name = $name;
    }

    public function isEqual(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
