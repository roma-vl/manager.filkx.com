<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Project\Filter;

use App\Model\Work\Entity\Members\Member\Status;

class Filter
{
    public ?string $name = null;
    public ?string $member = null;
    public string $account_id;
    public ?string $status = Status::ACTIVE;

    private function __construct(?string $member)
    {
        $this->member = $member;
    }

    public static function all(): self
    {
        return new self(null);
    }

    public static function forMember(string $id): self
    {
        return new self($id);
    }
}
