<?php

namespace App\Tests\Service;

use App\Service\AuthMailer;
use App\Entity\Account;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthMailerTest extends TestCase
{
    public function testSendForgotEmail()
    {
        // Mock des dépendances
        $routerMock = $this->createMock(RouterInterface::class);
        $mailerMock = $this->createMock(MailerInterface::class);

        // Mock du token et de l'URL générée
        $fakeToken = 'fake-token-123';
        $fakeEmail = 'test@example.com';
        $fakeUrl = 'https://example.com/reset-password/fake-token-123';

        // Simule le comportement du router pour générer l'URL
        $routerMock->method('generate')
            ->with('app_reset_password', ['uid' => $fakeToken], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn($fakeUrl);

        // Création de l'utilisateur factice
        $userMock = $this->createMock(Account::class);
        $userMock->method('getResetPasswordToken')->willReturn($fakeToken);
        $userMock->method('getEmail')->willReturn($fakeEmail);

        // On attend que la méthode send() soit appelée une fois avec un Email
        $mailerMock->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($fakeEmail, $fakeUrl) {
                return $email->getTo()[0]->getAddress() === $fakeEmail &&
                    $email->getSubject() === 'Réinitialisation de votre mot de passe' &&
                    str_contains($email->getHtmlBody(), $fakeUrl);
            }));

        // Initialisation du service avec les mocks
        $authMailer = new AuthMailer($mailerMock, $routerMock);

        // Appel de la méthode à tester
        $authMailer->sendForgotEmail($userMock);
    }
}
