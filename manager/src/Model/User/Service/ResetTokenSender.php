<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\RouterInterface;

class ResetTokenSender
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly RouterInterface $router,
        private readonly string $appUrl,
    ) {}

    public function send(Email $email, ResetToken $token): void
    {

        $path = $this->router->generate('auth.reset.reset', ['token' => $token->getToken()]);

        $resetUrl = rtrim($this->appUrl, '/') . $path;
        error_log('APP URL: ' . $this->appUrl);
        error_log('Reset URL: ' . $resetUrl);

        $message = (new TemplatedEmail())
            ->from('noreply@filkx.com')
            ->to($email->getValue())
            ->subject('Password resetting')
            ->htmlTemplate('mail/user/reset.html.twig')
            ->context([
                'resetUrl' => $resetUrl,
                'token' => $token->getToken(),
            ]);

        $this->mailer->send($message);
    }
}
