<?php

namespace App\Controller\Contact;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController 
{
    #[Route('/contact', name: 'app_contact_new', methods: ['GET', 'POST'])] 
    public function new(Request $request, EntityManagerInterface $entityManager): Response 
    {
        $contact = new Contact(); // Crée une nouvelle instance de Contact

        // Vérifie si l'utilisateur est connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->getUser(); // Récupère l'utilisateur connecté

            // Assure que l'utilisateur a bien une méthode getEmail()
            if (method_exists($user, 'getEmail')) {
                $contact->setUtilisateur($user); // Associe l'utilisateur au contact
                $contact->setEmail($user->getEmail()); // Récupère l'email de l'utilisateur
            } else {
                throw new \RuntimeException('La méthode getEmail() n\'est pas définie pour l\'utilisateur.'); // Erreur si la méthode n'existe pas
            }
        }

        // Crée le formulaire de contact, en passant l'instance Contact
        $form = $this->createForm(ContactType::class, $contact, [
            'is_authenticated' => $this->isGranted('IS_AUTHENTICATED_FULLY'), // Indique si l'utilisateur est authentifié
        ]);
        
        $form->handleRequest($request); // Traite la requête du formulaire

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setDateDenvoie(new \DateTime()); // Définit la date d'envoi
            $contact->setStatut(false); // Définit le statut du contact comme non traité

            $entityManager->persist($contact); // Prépare l'entité pour l'enregistrement
            $entityManager->flush(); // Enregistre le contact dans la base de données

            return $this->redirectToRoute('app_contact_new'); // Redirige vers la même page après envoi
        }

        // Rendu de la vue du formulaire
        return $this->render('contact/new.html.twig', [
            'form' => $form->createView(), // Génère la vue du formulaire
            'current_route' => 'formulaire', // Définit la route actuelle
        ]);
    }
}
