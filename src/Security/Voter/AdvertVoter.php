<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Security\Voter;

use App\Entity\Advert;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AdvertVoter extends Voter
{
    const EDIT = 'edit';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return \in_array($attribute, [self::EDIT], true)
            && $subject instanceof Advert;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $perm = match ($attribute) {
            self::EDIT => $this->canEdit($attribute, $subject),
            default => false,
        };

        return $perm;
    }

    private function canEdit(string $attribute, Advert $advert): bool
    {
        $user = $this->security->getUser();

        return $user->getId() === $advert->getId() || $user->getEmail() === $advert->getEmail();
    }
}
