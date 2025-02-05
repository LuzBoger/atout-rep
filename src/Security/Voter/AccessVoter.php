<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class AccessVoter extends Voter
{
    public const REPAIR_OBJECTS = 'repair_objects';
    public const REPAIR_HOUSE = 'repair_house';
    public const MARKETPLACE = 'marketplace_access';
    public const PRODUCT_MANAGEMENT = 'product_management';
    public const ADMIN_ZONE = 'admin_zone';
    public const USER_ZONE = 'user_zone';
    public const PROFIL_ZONE = 'profil_zone';
    public const PRESTA_ZONE = 'presta_zone';

    private array $permissions = [
        self::REPAIR_OBJECTS => ['ROLE_USER'],
        self::REPAIR_HOUSE => ['ROLE_USER'],
        self::MARKETPLACE => ['ROLE_USER', 'ROLE_ADMIN'],
        self::PRODUCT_MANAGEMENT => ['ROLE_PRESTA'],
        self::ADMIN_ZONE => ['ROLE_ADMIN'],
        self::USER_ZONE => ['ROLE_USER'],
        self::PRESTA_ZONE => ['ROLE_PRESTA'],
        self::PROFIL_ZONE => ['ROLE_USER', 'ROLE_PRESTA', 'ROLE_ADMIN'],
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        return array_key_exists($attribute, $this->permissions);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $roles = $user->getRoles();

        return !empty(array_intersect($roles, $this->permissions[$attribute]));
    }
}
