<?php

declare(strict_types=1);

namespace App\DataFixtures;

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
    public function __construct(
        private readonly PasswordHasher $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        echo "Loading user fixture...\n";
        echo 'DB URL: ' . $_ENV['DATABASE_URL'] . "\n";

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Name('Roma', 'Volkov'),
            new Email('Drakyla60@gmail.com'),
            '', // тимчасовий пароль
            'token'
        );

        $hash = $this->hasher->hash($user, '11111111');
        $user->updatePasswordHash($hash);

        $user->confirmSignUp();
        $user->changeRole(Role::admin());

        $manager->persist($user);
        $manager->flush();
    }
}
