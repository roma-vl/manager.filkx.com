<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\User\Entity\Account\Account;
use App\Model\User\Entity\Account\Id as AccountId;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const REFERENCE_ADMIN = 'user_user_admin';
    public const REFERENCE_USER = 'user_user_user';
    public const REFERENCE_ACCOUNT = 'user_account';
    public const REFERENCE_USER_ME = 'user_user_user_me';
    private $account;

    public function __construct(
        private readonly PasswordHasher $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        echo "Loading user fixture...\n";

        $this->account = Account::create(
            AccountId::next(),
            'Demo User',
            'en'
        );
        $manager->persist($this->account);
        $this->setReference(self::REFERENCE_ACCOUNT, $this->account);

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Name('Demo', 'User'),
            new Email('demo@filkx.com'),
            '',
            'token',
            $this->account
        );

        $hash = $this->hasher->hash($user, '11111111');
        $user->updatePasswordHash($hash);

        $user->confirmSignUp();

        $manager->persist($user);
        $this->setReference(self::REFERENCE_USER_ME, $user);

        $network = $this->createSignedUpByNetwork(
            new Name('David', 'Black'),
            'facebook',
            '1000000'
        );
        $manager->persist($network);

        $requested = $this->createSignUpRequestedByEmail(
            new Name('John', 'Doe'),
            new Email('requested@app.test'),
            '',
        );
        $hash = $this->hasher->hash($requested, '11111111');
        $requested->updatePasswordHash($hash);
        $manager->persist($requested);

        $confirmed = $this->createSignUpConfirmedByEmail(
            new Name('Brad', 'Pitt'),
            new Email('user@app.test'),
            ''
        );
        $hash = $this->hasher->hash($confirmed, '11111111');
        $confirmed->updatePasswordHash($hash);
        $manager->persist($confirmed);

        $this->setReference(self::REFERENCE_USER, $confirmed);

        $admin = $this->createAdminByEmail(
            new Name('James', 'Bond'),
            new Email('admin@app.test'),
            ''
        );
        $hash = $this->hasher->hash($admin, '11111111');
        $admin->updatePasswordHash($hash);
        $manager->persist($admin);

        $this->setReference(self::REFERENCE_ADMIN, $admin);

        $manager->flush();
    }

    private function createAdminByEmail(Name $name, Email $email, string $hash): User
    {
        return $this->createSignUpConfirmedByEmail($name, $email, $hash);
    }

    private function createSignUpConfirmedByEmail(Name $name, Email $email, string $hash): User
    {
        $user = $this->createSignUpRequestedByEmail($name, $email, $hash);
        $user->confirmSignUp();

        return $user;
    }

    private function createSignUpRequestedByEmail(Name $name, Email $email, string $hash): User
    {
        return User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            $name,
            $email,
            $hash,
            'token',
            $this->account
        );
    }

    private function createSignedUpByNetwork(Name $name, string $network, string $identity): User
    {
        return User::signUpByNetwork(
            Id::next(),
            new \DateTimeImmutable(),
            $name,
            $network,
            $identity,
            $this->account,
        );
    }
}
