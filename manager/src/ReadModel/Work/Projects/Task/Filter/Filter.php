<?php

declare(strict_types=1);

namespace App\ReadModel\Work\Projects\Task\Filter;

class Filter
{
    public ?string $member = null;
    public ?string $author = null;
    public ?string $project;
    public string $account_id;
    public ?string $text = null;
    public ?string $type = null;
    public ?string $status = null;
    public ?string $priority = null;
    public ?string $executor = null;
    public ?string $roots = null;

    private function __construct(?string $project)
    {
        $this->project = $project;
    }

    public static function forProject(string $project): self
    {
        return new self($project);
    }

    public static function all(): self
    {
        return new self(null);
    }

    public function withMember(?string $member): self
    {
        $clone = clone $this;
        $clone->member = $member;

        return $clone;
    }

    public function withAuthor(?string $author): self
    {
        $clone = clone $this;
        $clone->author = $author;

        return $clone;
    }

    public function withExecutor(?string $executor): self
    {
        $clone = clone $this;
        $clone->executor = $executor;

        return $clone;
    }

    public function withText(?string $text): self
    {
        $clone = clone $this;
        $clone->text = $text;

        return $clone;
    }

    public function withProject(?string $project): self
    {
        $clone = clone $this;
        $clone->project = $project;

        return $clone;
    }

    public function withType(?string $type): self
    {
        $clone = clone $this;
        $clone->type = $type;

        return $clone;
    }

    public function withStatus(?string $status): self
    {
        $clone = clone $this;
        $clone->status = $status;

        return $clone;
    }

    public function withPriority(?string $priority): self
    {
        $clone = clone $this;
        $clone->priority = $priority;

        return $clone;
    }

    public function withRoots(?string $roots): self
    {
        $clone = clone $this;
        $clone->roots = $roots;

        return $clone;
    }
}
