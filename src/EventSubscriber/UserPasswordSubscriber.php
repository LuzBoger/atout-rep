<?php

namespace App\EventSubscriber;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

#[AsEntityListener(event: Events::prePersist, method: 'hashPassword', entity: Account::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'hashPassword', entity: Account::class)]
class UserPasswordSubscriber
{
    public function __construct(
        protected UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    public function hashPassword(Account $user): void
    {
        dump($user);
        $plainPassword = $user->getPlainPassword();
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword(password: $hashedPassword);
            $user->eraseCredentials();

            // Envoie d'emails
        }
    }
}
