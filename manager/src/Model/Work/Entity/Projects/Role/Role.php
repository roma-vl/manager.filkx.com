<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Role;

use App\Model\User\Entity\Account\Account;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_projects_roles')]
class Role
{
    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_role_id')]
    private Id $id;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ORM\Column(type: 'string', unique: true)]
    private string $name;

    /**
     * @var Collection<int, Permission>
     */
    #[ORM\Column(type: 'work_projects_role_permissions')]
    private Collection $permissions;

    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    private int $version;

    /**
     * @param string[] $permissions
     */
    public function __construct(Id $id, string $name, array $permissions, Account $account)
    {
        $this->id = $id;
        $this->name = $name;
        $this->setPermissions($permissions);
        $this->account = $account;
    }

    /**
     * @param string[] $permissions
     */
    public function edit(string $name, array $permissions): void
    {
        $this->name = $name;
        $this->setPermissions($permissions);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions->exists(static function (int $_, Permission $current) use ($permission): bool {
            return $current->isNameEqual($permission);
        });
    }

    public function clone(Id $id, string $name): self
    {
        return new self(
            $id,
            $name,
            array_map(static fn (Permission $permission): string => $permission->getName(), $this->permissions->toArray())
        );
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return $this->permissions->toArray();
    }

    /**
     * @param string[] $names
     */
    public function setPermissions(array $names): void
    {
        $uniqueNames = array_unique($names);
        $this->permissions = new ArrayCollection(
            array_map(static fn (string $name): Permission => new Permission($name), $uniqueNames)
        );
    }
}
