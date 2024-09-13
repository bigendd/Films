<?php

namespace App\Controller\Admin\Contact;

use App\Entity\Contact;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contact')]
class AdminContactReponseController extends AbstractController
{
    #[Route('/{id}/respond', name: 'admin_contact_respond', methods: ['GET', 'POST'])]
    public function respond(Request $request, Contact $contact, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        // On crée le formulaire pour répondre au contact
        $form = $this->createForm(ReponseType::class);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère la réponse de l'admin et on la sauvegarde dans le contact
            $reponse = $form->get('reponse')->getData();
            $contact->setReponseAdmin($reponse);
            $contact->setStatut(true); // On archive le message après avoir répondu

            // On prépare l'email avec la réponse
            $email = (new Email())
                ->from('isetif149@gmail.com')  // L'adresse de l'expéditeur
                ->to($contact->getEmail())  // L'adresse du destinataire
                ->subject('Réponse à votre message')  // Sujet de l'email
                ->text($reponse);  // Contenu de l'email

            // On envoie l'email
            $mailer->send($email);

            // On sauvegarde les changements dans la base de données
            $entityManager->flush();

            // On redirige vers la liste des contacts après avoir répondu
            return $this->redirectToRoute('admin_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        // On affiche le formulaire pour répondre au contact
        return $this->render('admin/contact/respond.html.twig', [
            'contact' => $contact,  // Les informations du contact à afficher
            'form' => $form->createView(),  // La vue du formulaire
            'current_route' => 'admin',  // La route actuelle pour la vue
        ]);
    }
}
