<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Member;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
class Name
{
    #[ORM\Column(type: 'string')]
    private string $first;

    #[ORM\Column(type: 'string')]
    private string $last;

    public function __construct(string $first, string $last)
    {
        Assert::notEmpty($first, 'First name should not be empty.');
        Assert::notEmpty($last, 'Last name should not be empty.');

        $this->first = $first;
        $this->last = $last;
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getLast(): string
    {
        return $this->last;
    }

    public function getFull(): string
    {
        return $this->first . ' ' . $this->last;
    }
}
