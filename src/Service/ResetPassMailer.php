<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResetPassMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $router,
    ) {}

    public function send(string $token, User $user): void
    {
        $url = $this->router->generate('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Demande de réinitialisation de mot de passe')
            ->htmlTemplate('email/reset.html.twig')
            ->context([
                'user' => $user,
                'url' => $url,
            ])
        ;
        $this->mailer->send($email);
    }
}
