<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Task\Create;

use App\Model\User\Entity\Account\Account;
use App\Model\Work\Entity\Projects\Task\Type;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $project;

    #[Assert\NotBlank]
    public string $member;

    /** @var NameRow[] */
    #[Assert\NotBlank]
    #[Assert\Valid]
    public array $names = [];

    public ?string $content = null;

    public ?int $parent = null;

    #[Assert\Date]
    public ?\DateTimeImmutable $plan = null;

    #[Assert\NotBlank]
    public string $type;

    #[Assert\NotBlank]
    public int $priority;

    #[Assert\NotBlank]
    public Account $account;

    public function __construct(string $project, string $member, Account $account)
    {
        $this->project = $project;
        $this->member = $member;
        $this->type = Type::NONE;
        $this->priority = 2;
        $this->account = $account;
    }
}
