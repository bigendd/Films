<?php

namespace App\Controller\Registration;

use App\Entity\Utilisateur;
use App\Security\EmailVerifier;
use App\Entity\InfoUtilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class RegistrationController extends AbstractController
{
    // Injection du service EmailVerifier dans le constructeur
    public function __construct(private EmailVerifier $emailVerifier) {}

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Vérifie si l'utilisateur est déjà authentifié, dans ce cas, redirige vers la liste des films
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('film_list');
        }

        // Création de nouvelles instances d'Utilisateur et InfoUtilisateur
        $user = new Utilisateur();
        $infoUser = new InfoUtilisateur();

        // Création du formulaire d'inscription
        $form = $this->createForm(RegistrationFormType::class, null, [
            'data_class' => null,
        ]);

        $form->handleRequest($request); // Gère la requête HTTP pour le formulaire
        $redirect = $request->query->get('redirect', $this->generateUrl('film_list'));

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère l'email du formulaire
            $email = $form->get('email')->getData();

            // Vérifie si un utilisateur existe déjà avec cet email
            $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                // Lance une exception si un utilisateur avec cet email existe déjà
                $this->addFlash('error', 'Vous avez déjà un compte avec cet email.');
                return $this->redirectToRoute('app_login');  // Redirige vers la page de connexion
            }

            // Récupération et hachage du mot de passe
            $user->setEmail($email);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);

            // Configuration des informations supplémentaires de l'utilisateur
            $infoUser->setDateDeCreation(new \DateTime());
            $infoUser->setUtilisateur($user);

            // Persiste l'utilisateur et les informations supplémentaires dans la base de données
            $entityManager->persist($user);
            $entityManager->persist($infoUser);
            $entityManager->flush();

             // Envoi d'un e-mail de confirmation d'inscription
            // $this->emailVerifier->sendEmailConfirmation(
            //     'app_verify_email',
            //     $user,
            //     (new TemplatedEmail())
            //         ->from(new Address('amarbelaifa8@gmail.com', 'mail bot'))
            //         ->to($user->getEmail())
            //         ->subject('Veuillez confirmer votre email')
            //         ->htmlTemplate('registration/confirmation_email.html.twig')
            // );

            return $security->login($user, 'form_login', 'main');        }

        // Rendre la vue d'inscription avec le formulaire
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(), // Passe le formulaire à la vue
            'redirect' => $redirect, // Passe l'URL de redirection à la vue
            'current_route' => 'formulaire', // Passe la route actuelle à la vue
        ]);
    }

    // #[Route('/verify/email', name: 'app_verify_email')]
    // public function verifyUserEmail(Request $request): Response
    // {
    //     // Vérifie si l'utilisateur est authentifié
    //     $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    //     try {
    //         // Gère la confirmation de l'e-mail
    //         $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
    //     } catch (VerifyEmailExceptionInterface $exception) {

    //         // Redirige vers la page d'inscription en cas d'erreur
    //         return $this->redirectToRoute('app_register');
    //     }


    //     // Redirige vers la page d'inscription
    //     return $this->redirectToRoute('app_register');
    // }
}
