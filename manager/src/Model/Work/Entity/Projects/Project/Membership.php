<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Department\Department;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'work_projects_project_memberships')]
#[ORM\UniqueConstraint(columns: ['project_id', 'member_id'])]
class Membership
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false)]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', nullable: false)]
    private Member $member;

    /** @var Collection<int, Department> */
    #[ORM\ManyToMany(targetEntity: Department::class)]
    #[ORM\JoinTable(
        name: 'work_projects_project_membership_departments',
        joinColumns: [new ORM\JoinColumn(name: 'membership_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id')]
    )]
    private Collection $departments;

    /** @var Collection<int, Role> */
    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(
        name: 'work_projects_project_membership_roles',
        joinColumns: [new ORM\JoinColumn(name: 'membership_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')]
    )]
    private Collection $roles;

    /**
     * @param Department[] $departments
     * @param Role[]       $roles
     */
    public function __construct(Project $project, Member $member, array $departments, array $roles)
    {
        $this->guardDepartments($departments);
        $this->guardRoles($roles);

        $this->id = Uuid::uuid4()->toString();
        $this->project = $project;
        $this->member = $member;
        $this->departments = new ArrayCollection($departments);
        $this->roles = new ArrayCollection($roles);
    }

    /**
     * @param Department[] $departments
     */
    public function changeDepartments(array $departments): void
    {
        $this->guardDepartments($departments);

        $current = $this->departments->toArray();
        $new = $departments;

        $compare = static fn (Department $a, Department $b): int => $a->getId()->getValue() <=> $b->getId()->getValue();

        foreach (array_udiff($current, $new, $compare) as $item) {
            $this->departments->removeElement($item);
        }

        foreach (array_udiff($new, $current, $compare) as $item) {
            $this->departments->add($item);
        }
    }

    /**
     * @param Role[] $roles
     */
    public function changeRoles(array $roles): void
    {
        $this->guardRoles($roles);

        $current = $this->roles->toArray();
        $new = $roles;

        $compare = static fn (Role $a, Role $b): int => $a->getId()->getValue() <=> $b->getId()->getValue();

        foreach (array_udiff($current, $new, $compare) as $item) {
            $this->roles->removeElement($item);
        }

        foreach (array_udiff($new, $current, $compare) as $item) {
            $this->roles->add($item);
        }
    }

    public function isForMember(MemberId $id): bool
    {
        return $this->member->getId()->isEqual($id);
    }

    public function isForDepartment(DepartmentId $id): bool
    {
        foreach ($this->departments as $department) {
            if ($department->getId()->isEqual($id)) {
                return true;
            }
        }

        return false;
    }

    public function isGranted(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles->toArray();
    }

    /**
     * @return Department[]
     */
    public function getDepartments(): array
    {
        return $this->departments->toArray();
    }

    /**
     * @param Department[] $departments
     */
    private function guardDepartments(array $departments): void
    {
        if (\count($departments) === 0) {
            throw new \DomainException('Set at least one department.');
        }
    }

    /**
     * @param Role[] $roles
     */
    private function guardRoles(array $roles): void
    {
        if (\count($roles) === 0) {
            throw new \DomainException('Set at least one role.');
        }
    }
}
