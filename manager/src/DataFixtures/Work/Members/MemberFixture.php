<?php

declare(strict_types=1);

namespace App\DataFixtures\Work\Members;

use App\DataFixtures\UserFixture;
use App\Model\User\Entity\Account\Account;
use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Member\Email;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Name;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixture extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_ADMIN = 'work_member_admin';
    public const REFERENCE_USER = 'work_member_user';
    public const REFERENCE_USER_ME = 'work_member_user_me';

    public function load(ObjectManager $manager): void
    {
        /** @var User $admin */
        $admin = $this->getReference(UserFixture::REFERENCE_ADMIN, User::class);

        /** @var User $user */
        $user = $this->getReference(UserFixture::REFERENCE_USER, User::class);
        $userMe = $this->getReference(UserFixture::REFERENCE_USER_ME, User::class);

        /** @var Group $staff */
        $staff = $this->getReference(GroupFixture::REFERENCE_STAFF, Group::class);

        /** @var Group $customers */
        $customers = $this->getReference(GroupFixture::REFERENCE_CUSTOMERS, Group::class);

        $member = $this->createMember($admin, $staff);
        $manager->persist($member);
        $this->setReference(self::REFERENCE_ADMIN, $member);

        $member = $this->createMember($user, $customers);
        $manager->persist($member);

        $memberMe = $this->createMember($userMe, $customers);
        $manager->persist($memberMe);
        $this->setReference(self::REFERENCE_USER, $member);
        $this->setReference(self::REFERENCE_USER_ME, $memberMe);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            GroupFixture::class,
        ];
    }

    private function createMember(User $user, Group $group): Member
    {
        $account = $this->getReference(UserFixture::REFERENCE_ACCOUNT, Account::class);
        return new Member(
            new Id($user->getId()->getValue()),
            $group,
            new Name(
                $user->getName()->getFirst(),
                $user->getName()->getLast()
            ),
            new Email($user->getEmail() ? $user->getEmail()->getValue() : null),
            $account
        );
    }
}
