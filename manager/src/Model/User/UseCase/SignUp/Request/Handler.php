<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Flusher;
use App\Model\User\Entity\Account\Account;
use App\Model\User\Entity\Account\AccountRepository;
use App\Model\User\Entity\Account\Id as AccountId;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\SignUpConfirmTokenizer;
use App\Model\User\Service\SignUpConfirmTokenSender;
use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Group\GroupRepository;
use App\Model\Work\Entity\Members\Group\Id as MembersGroupId;
use App\Model\Work\Entity\Members\Member\Email as MemberEmail;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Members\Member\Name as MemberName;

class Handler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AccountRepository $accountRepository,
        private readonly GroupRepository $groupRepository,
        private readonly MemberRepository $memberRepository,
        private readonly PasswordHasher $hasher,
        private readonly SignUpConfirmTokenizer $tokenizer,
        private readonly SignUpConfirmTokenSender $sender,
        private readonly Flusher $flusher,
    ) {
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->userRepository->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }
        $account = Account::create(
            AccountId::next(),
            $command->firstName . ' ' .  $command->lastName,
            'en'
        );

        $user = User::signUpByEmail(
            Id::next(),
            new \DateTimeImmutable(),
            new Name(
                $command->firstName,
                $command->lastName
            ),
            $email,
            'placeholder',
            $token = $this->tokenizer->generate(),
            $account
        );

        $hashedPassword = $this->hasher->hash($user, $command->password);
        $user->updatePasswordHash($hashedPassword);

        $this->userRepository->add($user);

        $account->setOwner($user);

        $this->accountRepository->add($account);

        $group = new Group(
            MembersGroupId::next(),
            'Default Group',
            $account
        );

        $this->groupRepository->add($group);

        $member = new Member(
            new MemberId($user->getId()->getValue()),
            $group,
            new MemberName(
                $command->firstName,
                $command->lastName
            ),
            new MemberEmail($command->email),
            $account
        );

        $this->memberRepository->add($member);

        $this->sender->send($email, $token);
        $this->flusher->flush();
    }
}
