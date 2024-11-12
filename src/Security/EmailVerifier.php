<?php
namespace App\Security;

use App\Entity\Utilisateur;  // Utilisation de la classe Utilisateur pour manipuler les utilisateurs
use Doctrine\ORM\EntityManagerInterface;  // Utilisation de l'EntityManager pour persister les entités en base de données
use Symfony\Bridge\Twig\Mime\TemplatedEmail;  // Utilisation de TemplatedEmail pour envoyer des emails avec un template
use Symfony\Component\HttpFoundation\Request;  // Utilisation de Request pour récupérer des informations depuis la requête HTTP
use Symfony\Component\Mailer\MailerInterface;  // Interface Mailer pour envoyer des emails
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;  // Gestion des exceptions liées à la vérification des emails
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;  // Interface pour générer des signatures pour la vérification des emails

// Définition de la classe EmailVerifier
class EmailVerifier
{
    // Constructeur qui injecte les dépendances nécessaires pour la vérification des emails
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,  // Service pour générer la signature de vérification
        private MailerInterface $mailer,  // Service pour envoyer les emails
        private EntityManagerInterface $entityManager  // Service pour persister l'utilisateur après la vérification
    ) {
    }

    // Envoie un email de confirmation avec un lien pour vérifier l'adresse email de l'utilisateur
    public function sendEmailConfirmation(string $verifyEmailRouteName, Utilisateur $user, TemplatedEmail $email): void
    {
        // Génération de la signature de vérification avec l'ID et l'email de l'utilisateur
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,  // Route utilisée pour la vérification de l'email
            (string) $user->getId(),  // ID de l'utilisateur (converti en chaîne)
            $user->getEmail()  // Email de l'utilisateur
        );

        // Récupération du contexte actuel de l'email pour y ajouter des informations sur la signature
        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();  // URL signée pour vérifier l'email
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();  // Message clé pour l'expiration du lien
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();  // Données liées à l'expiration du lien

        // Mise à jour du contexte de l'email avec les nouvelles données
        $email->context($context);

        // Envoi de l'email via le service Mailer
        $this->mailer->send($email);
    }

    /**
     * Gère la confirmation de l'email depuis la requête HTTP.
     *
     * @throws VerifyEmailExceptionInterface En cas d'erreur lors de la vérification de l'email
     */
    public function handleEmailConfirmation(Request $request, Utilisateur $user): void
    {
        // Validation de la confirmation de l'email à partir de la requête
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string) $user->getId(), $user->getEmail());

        // Mise à jour de l'utilisateur pour indiquer qu'il a vérifié son email
        $user->setVerified(true);

        // Persistance des changements de l'utilisateur dans la base de données
        $this->entityManager->persist($user);  // Prépare l'utilisateur à être enregistré
        $this->entityManager->flush();  // Sauvegarde définitivement l'utilisateur avec son email vérifié
    }
}
