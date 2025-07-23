<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Task;

use Webmozart\Assert\Assert;

class Priority
{
    public const LOW = 'low';
    public const NORMAL = 'normal';
    public const FEATURE = 'feature';
    public const HIGH = 'high';
    public const CRITICAL = 'critical';
    public const BLOCKER = 'blocker';
    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::LOW,
            self::NORMAL,
            self::FEATURE,
            self::HIGH,
            self::CRITICAL,
            self::BLOCKER,
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
