<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ResetTokenSender
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {}

    public function send(Email $email, ResetToken $token): void
    {
        $message = (new TemplatedEmail())
            ->from('noreply@filkx.com')
            ->to($email->getValue())
            ->subject('Password resetting')
            ->htmlTemplate('mail/user/reset.html.twig')
            ->context([
                'token' => $token->getToken(),
            ]);

        $this->mailer->send($message);
    }
}
