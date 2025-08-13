<?php

declare(strict_types=1);

namespace App\DataFixtures\Work\Projects;

use App\DataFixtures\UserFixture;
use App\DataFixtures\Work\Members\MemberFixture;
use App\Model\User\Entity\Account\Account;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixture extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_FIRST = 'first';
    public const REFERENCE_SECOND = 'second';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(UserFixture::REFERENCE_ACCOUNT, Account::class);
        $admin = $this->getReference(MemberFixture::REFERENCE_ADMIN, Member::class);
        $user = $this->getReference(MemberFixture::REFERENCE_USER, Member::class);
        $userMe = $this->getReference(MemberFixture::REFERENCE_USER_ME, Member::class);

        $manage = $this->getReference(RoleFixture::REFERENCE_MANAGER, Role::class);
        $guest = $this->getReference(RoleFixture::REFERENCE_GUEST, Role::class);

        $active = $this->createProject('First Project', 1);

        $active->addDepartment(
            $development = DepartmentId::next(),
            'Development',
            $account
        );
        $active->addDepartment(
            $marketing = DepartmentId::next(),
            'Marketing',
            $account
        );
        $active->addMember($admin, [$development], [$manage]);
        $active->addMember($user, [$marketing], [$guest]);
        $active->addMember($userMe, [$marketing], [$manage]);

        $manager->persist($active);
        $this->setReference(self::REFERENCE_FIRST, $active);

        $active = $this->createProject('Second Project', 2);

        $active->addDepartment(
            $development = DepartmentId::next(),
            'Development',
            $account);
        $active->addMember($admin, [$development], [$guest]);

        $manager->persist($active);
        $this->setReference(self::REFERENCE_SECOND, $active);

        $archived = $this->createArchivedProject('Third Project', 3);
        $manager->persist($archived);

        $manager->flush();
    }

    private function createArchivedProject(string $name, int $sort): Project
    {
        $project = $this->createProject($name, $sort);
        $project->archive();

        return $project;
    }

    private function createProject(string $name, int $sort): Project
    {
        $account = $this->getReference(UserFixture::REFERENCE_ACCOUNT, Account::class);
        return new Project(
            Id::next(),
            $name,
            $sort,
            $account
        );
    }

    public function getDependencies(): array
    {
        return [
            MemberFixture::class,
            RoleFixture::class,
        ];
    }
}
