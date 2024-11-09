<?php

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


        // Vérifiez si l'utilisateur est banni
        $bannissement = $user->getBannissement();
        if ($bannissement) {
            if ($bannissement->isBanne()) {
                $jourRestant = $bannissement->getJoursRestant();
                
                if ($jourRestant !== null && $jourRestant > 0 && $jourRestant <= 7) {
                    throw new CustomUserMessageAccountStatusException('Votre compte a été banni pour ' . $jourRestant . ' jours.');
                } elseif ($bannissement->isDefinitif()) {
                    throw new CustomUserMessageAccountStatusException('Votre compte a été banni définitivement veuillez nous contactez pour des information.');
                } else {
                    throw new CustomUserMessageAccountStatusException('Votre compte a été banni.');
                }
            }

            // Si le bannissement est expiré, on le supprime
            if ($bannissement->banneExpiree()) {
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
