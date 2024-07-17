<?php
// src/Security/UserChecker.php

namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        // Débogage
        error_log('UserChecker: checkPreAuth called for user ' . $user->getEmail());

        // Vérifiez si l'utilisateur est banni
        $bannissement = $user->getBannissement();
        if ($bannissement && $bannissement->isBanned()) {
            $remainingDays = $bannissement->getRemainingDays();
            if ($remainingDays !== null && $remainingDays <= 7) {
                error_log('UserChecker: User ' . $user->getEmail() . ' is banned for ' . $remainingDays . ' days.');
                throw new CustomUserMessageAccountStatusException('Votre compte a été banni pour ' . $remainingDays . ' jours.');
            } elseif ($bannissement->isDefinitif()) {
                throw new CustomUserMessageAccountStatusException('Votre compte a été banni définitivement.');
            } else {
                throw new CustomUserMessageAccountStatusException('Votre compte a été banni.');
            }
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Laissez vide si vous n'avez pas de vérification après l'authentification
    }
}
