<?php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\InfoUtilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $infoUser = new InfoUtilisateur();

        // Create the form with the data class set to null
        $form = $this->createForm(RegistrationFormType::class, null, [
            'data_class' => null, // Set data_class to null to accept array data
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the email from the form data
            $user->setEmail($form->get('email')->getData());

            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Set InfoUtilisateur fields from form data, checking for null values
            $infoUser->setNom($form->get('nom')->getData() ?? '');
            $infoUser->setPrenom($form->get('prenom')->getData() ?? '');
            $infoUser->setDateDeNaissance($form->get('dateDeNaissance')->getData());
            $infoUser->setAdressePostale($form->get('adressePostale')->getData() ?? '');
            $infoUser->setCodePostale($form->get('codePostale')->getData());
            $infoUser->setNumeroDeTelephone($form->get('numeroDeTelephone')->getData() ?? '');

            // Set the date_de_creation to the current datetime
            $infoUser->setDateDeCreation(new \DateTime());

            // Set the InfoUtilisateur object to the user
            $infoUser->setUtilisateur($user);

            // Persist both entities
            $entityManager->persist($user);
            $entityManager->persist($infoUser);
            $entityManager->flush();

            // Redirect or further actions
            return $this->redirectToRoute('film_list');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
}
