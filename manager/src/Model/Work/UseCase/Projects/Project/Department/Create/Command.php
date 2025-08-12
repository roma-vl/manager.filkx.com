<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Projects\Project\Department\Create;

use App\Model\User\Entity\Account\Account;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    public string $project;

    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    public Account $account;

    public function __construct(string $project)
    {
        $this->project = $project;
    }
}
