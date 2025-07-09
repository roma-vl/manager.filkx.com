<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Twig\Environment;

class SignUpConfirmTokenSender
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(Email $email, string $token): void
    {
        $body = $this->twig->render('mail/user/signup.html.twig', [
            'token' => $token,
        ]);

        $message = (new SymfonyEmail())
            ->from('noreply@yourdomain.com') // 🔁 Замінити на реальний email
            ->to($email->getValue())
            ->subject('Sign Up Confirmation')
            ->html($body);

        try {
            $this->mailer->send($message);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Unable to send confirmation email: ' . $e->getMessage(), 0, $e);
        }
    }
}
