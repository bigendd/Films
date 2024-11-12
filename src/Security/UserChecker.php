<?php
namespace App\Security;

use App\Entity\Utilisateur;  // Importation de la classe Utilisateur pour vérifier l'état de l'utilisateur
use Doctrine\ORM\EntityManagerInterface;  // Utilisation de l'EntityManager pour manipuler les entités en base de données
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;  // Exception personnalisée pour signaler un problème de statut de compte
use Symfony\Component\Security\Core\User\UserCheckerInterface;  // Interface pour vérifier les utilisateurs avant ou après authentification
use Symfony\Component\Security\Core\User\UserInterface;  // Interface représentant l'utilisateur dans Symfony

// Définition de la classe UserChecker qui implémente l'interface UserCheckerInterface
class UserChecker implements UserCheckerInterface
{
    // Déclaration de l'EntityManager pour accéder à la base de données
    private $entityManager;

    // Constructeur pour injecter l'EntityManager dans le service
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Vérification avant l'authentification (prérequis pour se connecter)
    public function checkPreAuth(UserInterface $user): void
    {
        // Vérification si l'utilisateur est bien une instance de l'entité Utilisateur
        if (!$user instanceof Utilisateur) {
            return;  // Si ce n'est pas le cas, on arrête l'exécution
        }

        // Vérification du statut de bannissement de l'utilisateur
        $bannissement = $user->getBannissement();
        if ($bannissement) {
            if ($bannissement->isBanne()) {  // Si l'utilisateur est banni
                $jourRestant = $bannissement->getJoursRestant();  // Récupération du nombre de jours restants de bannissement
                
                if ($jourRestant !== null && $jourRestant > 0 && $jourRestant <= 7) {
                    // Si le bannissement est de moins de 7 jours
                    throw new CustomUserMessageAccountStatusException('Votre compte a été banni pour ' . $jourRestant . ' jours.');
                } elseif ($bannissement->isDefinitif()) {
                    // Si le bannissement est définitif
                    throw new CustomUserMessageAccountStatusException('Votre compte a été banni définitivement, veuillez nous contacter pour plus d\'informations.');
                } else {
                    // Si le bannissement est actif mais pas définitif
                    throw new CustomUserMessageAccountStatusException('Votre compte a été banni.');
                }
            }

            // Si le bannissement est expiré, on le supprime de la base de données
            if ($bannissement->banneExpiree()) {
                $this->entityManager->remove($bannissement);  // Suppression de l'entité bannissement
                $this->entityManager->flush();  // Sauvegarde des changements en base
                error_log('UserChecker: Bannissement expiré pour l\'utilisateur ' . $user->getEmail() . '. Bannissement supprimé.');
            }
        }
    }

    // Vérification post-authentification, laissée vide pour l'instant
    public function checkPostAuth(UserInterface $user): void
    {
        // Ajoutez ici des vérifications supplémentaires si nécessaire après l'authentification
    }
}
