<?php

namespace App\Service;

use App\Entity\Account;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthMailer
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $router;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function sendForgotEmail(Account $user): void
    {
        $resetLink = $this->router->generate('app_reset_password', ['uid' => $user->getResetPasswordToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new Email())
            ->from('noreply@atoutrep.com')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->html(sprintf(
                '<p>Bonjour,</p><p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :</p><p><a href="%s">%s</a></p>',
                $resetLink,
                'Réinitialiser mon mot de passe'
            ));

        $this->mailer->send($email);
    }
}