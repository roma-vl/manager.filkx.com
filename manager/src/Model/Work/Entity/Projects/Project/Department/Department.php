<?php

declare(strict_types=1);

namespace App\Model\Work\Entity\Projects\Project\Department;

use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_projects_project_departments')]
class Department
{
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false)]
    private Project $project;

    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_project_department_id')]
    private Id $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    public function __construct(Project $project, Id $id, string $name)
    {
        $this->project = $project;
        $this->id = $id;
        $this->name = $name;
    }

    public function isNameEqual(string $name): bool
    {
        return $this->name === $name;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function edit(string $name): void
    {
        if ($this->name === $name) {
            return; // без змін — нічого не робимо
        }

        $this->name = $name;
    }
}
