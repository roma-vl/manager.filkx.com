<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\User\Entity\Account\Account;
use App\Model\User\Entity\Account\Id as AccountId;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\PasswordHasher;
use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\Id as TaskId;
use App\Model\Work\Entity\Projects\Task\Type;
use App\Model\Work\Entity\Projects\Task\Type as TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:demo:seed', description: 'Створює або оновлює демо-дані (акаунт, юзери, проекти, таски)')]
class SeedDemoCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly PasswordHasher $hasher,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $accountRepo = $this->em->getRepository(Account::class);
        $userRepo    = $this->em->getRepository(User::class);
        $projectRepo = $this->em->getRepository(Project::class);
        $memberRepo = $this->em->getRepository(Member::class);

        // === 1. Знаходимо або створюємо демо-акаунт
        $user = $memberRepo->findOneBy(['email' => 'demo@filkx.com']);
        $account = $accountRepo->findOneBy(['id' => $user->getId()->getValue()]);
        if (!$account) {
            $account = new Account(AccountId::next(), 'demo@filkx.com');
            $this->em->persist($account);
            $io->success('Створено демо-акаунт demo@filkx.com');
        } else {
            $io->info('Демо-акаунт вже існує');
        }

        // === 2. Створюємо демо-користувача (адміна)
        $user = $userRepo->findOneBy(['email' => 'demo@filkx.com']);
        if (!$user) {

            $user = User::signUpByEmail(
                Id::next(),
                new \DateTimeImmutable(),
                new Name('Demo', 'User'),
                new Email('demo@filkx.com'),
                '',
                'token',
                $account
            );

            $hash = $this->hasher->hash($user, '11111111');
            $user->updatePasswordHash($hash);

            $user->confirmSignUp();

            $this->em->persist($user);
            $io->success('Створено користувача demo@example.com (пароль: demo)');
        }

        // === 3. Група учасників
        $group = $this->em->getRepository(Group::class)->findOneBy(['name' => 'Demo Group']);
        if (!$group) {
            $group = new Group(
                \App\Model\Work\Entity\Members\Group\Id::next(),
                'Demo Group',
                $account
            );
            $this->em->persist($group);
            $io->success('Створено групу Demo Group');
        }

        $member = $this->em->getRepository(Member::class)->findOneBy(['email' => 'demo@filkx.com']);
        if (!$member) {
            $member =  new Member(
                new \App\Model\Work\Entity\Members\Member\Id($user->getId()->getValue()),
                $group,
                new \App\Model\Work\Entity\Members\Member\Name(
                    $user->getName()->getFirst(),
                    $user->getName()->getLast()
                ),
                new \App\Model\Work\Entity\Members\Member\Email($user->getEmail() ? $user->getEmail()->getValue() : null),
                $account
            );
            $this->em->persist($member);
            $io->success('Створено групу Demo Group');
        }

        // === 4. Додаємо демо-проекти
        $project = $projectRepo->findOneBy(['name' => 'Demo Project']);
        if (!$project) {
            $project = new Project(\App\Model\Work\Entity\Projects\Project\Id::next(), 'Demo Project', 2, $account);
            $this->em->persist($project);
            $io->success('Створено Demo Project');
        }

        // === 5. Додаємо демо-таски
        $taskRepo = $this->em->getRepository(Task::class);
        if (!$taskRepo->findOneBy(['name' => 'Demo Task #1'])) {
            $task = new Task(
                new TaskId(1),
                $project,
                $member,
                new \DateTimeImmutable(),
                new Type(TaskType::FEATURE),
                3,
                'Demo Task #1',
                'Це перша демо-таска',
                $account
            );
            $this->em->persist($task);
            $io->success('Створено Demo Task #1');
        }

        if (!$taskRepo->findOneBy(['name' => 'Demo Task #2'])) {
            $task = new Task(
                new TaskId(2),
                $project,
                $member,
                new \DateTimeImmutable(),
                new Type(TaskType::BUG),
                2,
                'Demo Task #2',
                'Це друга демо-таска',
                $account
            );
            $this->em->persist($task);
            $io->success('Створено Demo Task #2');
        }

        $this->em->flush();
        $io->success('✅ Демо-дані успішно створені/оновлені');

        return Command::SUCCESS;
    }
}
