<?php
// src/Controller/RegistrationController.php

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





class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }
    #[Route('/register', name: 'app_register')]

    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,Security $security): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('film_list');
        }
        $user = new Utilisateur();
        $infoUser = new InfoUtilisateur();

        $form = $this->createForm(RegistrationFormType::class, null, [
            'data_class' => null,
        ]);

        $form->handleRequest($request);
        $redirect = $request->query->get('redirect', $this->generateUrl('film_list'));

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEmail($form->get('email')->getData());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

           // $infoUser->setNom($form->get('nom')->getData() ?? '');
           // $infoUser->setPrenom($form->get('prenom')->getData() ?? '');
           // $infoUser->setDateDeNaissance($form->get('dateDeNaissance')->getData());
           // $infoUser->setAdressePostale($form->get('adressePostale')->getData() ?? '');
           // $infoUser->setCodePostale($form->get('codePostale')->getData());
         //   $infoUser->setNumeroDeTelephone($form->get('numeroDeTelephone')->getData() ?? '');
           $infoUser->setDateDeCreation(new \DateTime());
            $infoUser->setUtilisateur($user);

            $entityManager->persist($user);
            $entityManager->persist($infoUser);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('amarbelaifa8@gmail.com', 'mail bot'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'redirect' => $redirect,
            'current_route' => 'formulaire', 
        ]);
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
