<?php

namespace App\Controller\PasswordReset;

use App\Entity\Utilisateur;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

// Définition de la route de base pour les opérations de réinitialisation de mot de passe
#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait; // Inclusion du trait pour gérer les fonctionnalités de réinitialisation de mot de passe

    // Constructeur pour injecter les dépendances nécessaires
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    // Route pour afficher le formulaire de demande de réinitialisation de mot de passe
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        // Création du formulaire pour la demande de réinitialisation
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request); // Traitement de la requête

        // Vérification si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Envoi de l'e-mail de réinitialisation si le formulaire est valide
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(), // Récupération de l'e-mail depuis le formulaire
                $mailer
            );
        }

        // Rendu du formulaire de demande de réinitialisation
        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
            'current_route' => 'formulaire', // Passer la route actuelle pour la navigation
        ]);
    }

    // Route pour vérifier l'e-mail après une demande de réinitialisation
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Récupération du token de réinitialisation depuis la session, ou génération d'un faux token si introuvable
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        // Rendu de la page de vérification de l'e-mail
        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken, // Passer le token à la vue
            'current_route' => 'formulaire' // Passer la route actuelle pour la navigation
        ]);
    }

    // Route pour réinitialiser le mot de passe avec le token fourni
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    {
        // Vérification si un token est présent dans l'URL
        if ($token) {
            // Stockage du token dans la session pour éviter les fuites
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_reset_password'); // Redirection vers la même route sans token dans l'URL
        }

        // Récupération du token de la session
        $token = $this->getTokenFromSession();

        // Vérification si un token est disponible
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var Utilisateur $user */
            // Validation du token et récupération de l'utilisateur associé
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            // Gestion des erreurs lors de la validation du token
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request'); // Redirection en cas d'erreur
        }

        // Création du formulaire pour changer le mot de passe
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request); // Traitement de la requête

        // Vérification si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Suppression de la demande de réinitialisation après utilisation du token
            $this->resetPasswordHelper->removeResetRequest($token);

            // Hachage du nouveau mot de passe et mise à jour de l'utilisateur
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData() // Récupération du mot de passe en clair
            );

            $user->setPassword($encodedPassword); // Mise à jour du mot de passe de l'utilisateur
            $this->entityManager->flush(); // Sauvegarde des modifications en base de données

            // Nettoyage de la session après la réinitialisation du mot de passe
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_home'); // Redirection vers la page d'accueil
        }

        // Rendu du formulaire de réinitialisation
        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
            'current_route' => 'formulaire' // Passer la route actuelle pour la navigation
        ]);
    }

    // Méthode pour traiter l'envoi de l'e-mail de réinitialisation de mot de passe
    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        // Recherche de l'utilisateur par e-mail
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Ne pas révéler si un compte utilisateur existe ou non
        if (!$user) {
            return $this->redirectToRoute('app_check_email'); // Redirection vers la vérification de l'e-mail
        }

        try {
            // Génération d'un token de réinitialisation pour l'utilisateur
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('app_check_email'); // Redirection en cas d'erreur
        }

        // Création de l'e-mail de réinitialisation
        $email = (new TemplatedEmail())
            ->from(new Address('amarbelaifa8@gmail.com', 'mail bot')) // Adresse de l'expéditeur
            ->to($user->getEmail()) // Adresse de l'utilisateur
            ->subject('Votre demande de réinitialisation de mot de passe') // Sujet de l'e-mail
            ->htmlTemplate('reset_password/email.html.twig') // Template HTML de l'e-mail
            ->context([
                'resetToken' => $resetToken, // Passer le token au template
            ]);

        $mailer->send($email); // Envoi de l'e-mail

        // Stockage de l'objet token dans la session pour la récupération ultérieure
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email'); // Redirection vers la vérification de l'e-mail
    }
}
