<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\Flusher;
use App\Model\User\Entity\Account\Account;
use App\Model\User\Entity\Account\AccountRepository;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\Account\Id as AccountId;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHasher;
use App\Model\User\Service\SignUpConfirmTokenizer;
use App\Model\User\Service\SignUpConfirmTokenSender;

class Handler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AccountRepository $accountRepository,
        private readonly PasswordHasher $hasher,
        private readonly SignUpConfirmTokenizer $tokenizer,
        private readonly SignUpConfirmTokenSender $sender,
        private readonly Flusher $flusher,
    ) {}

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->userRepository->hasByEmail($email)) {
            throw new \DomainException('User already exists.');
        }
        $account = Account::create(
            AccountId::next(),
            'Мій перший акаунт',
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

        $this->sender->send($email, $token);
        $this->flusher->flush();
    }
}
