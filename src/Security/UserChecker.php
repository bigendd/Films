<?php
// src/Security/UserChecker.php

namespace App\Security;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        // Débogage
        error_log('UserChecker: checkPreAuth called for user ' . $user->getEmail());

        // Vérifiez si l'utilisateur est banni
        $bannissement = $user->getBannissement();
        if ($bannissement) {
            if ($bannissement->isBanned()) {
                $remainingDays = $bannissement->getRemainingDays();
                
                if ($remainingDays !== null && $remainingDays > 0 && $remainingDays <= 7) {
                    error_log('UserChecker: User ' . $user->getEmail() . ' is banned for ' . $remainingDays . ' days.');
                    throw new CustomUserMessageAccountStatusException('Votre compte a été banni pour ' . $remainingDays . ' jours.');
                } elseif ($bannissement->isDefinitif()) {
                    throw new CustomUserMessageAccountStatusException('Votre compte a été banni définitivement.');
                } else {
                    throw new CustomUserMessageAccountStatusException('Votre compte a été banni.');
                }
            }

            // Si le bannissement est expiré, on le supprime
            if ($bannissement->isBannissementExpired()) {
                $this->entityManager->remove($bannissement);
                $this->entityManager->flush();
                error_log('UserChecker: Bannissement expiré pour l\'utilisateur ' . $user->getEmail() . '. Bannissement supprimé.');
            }
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Vérifications post-authentification si nécessaire
    }
}
