<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Members\Group;

use App\Model\User\Entity\Account\Account;
use App\Model\Work\Entity\Members\Member\Member;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_members_groups')]
class Group
{
    #[ORM\Id]
    #[ORM\Column(type: 'work_members_group_id')]
    private Id $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    private int $version;

    #[ORM\OneToMany(targetEntity: Member::class, mappedBy: 'group')]
    private Collection $members;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    public function __construct(Id $id, string $name, Account $account)
    {
        $this->id = $id;
        $this->name = $name;
        $this->account = $account;
    }

    public function edit(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
