<?php
// src/Controller/Admin/AdminContactController.php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Form\ResponseType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contact')]
class AdminContactController extends AbstractController
{
    #[Route('/', name: 'admin_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupérer uniquement les contacts non archivés (statut = 0)
        $contacts = $contactRepository->findBy(['statut' => 0]);

        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    #[Route('/{id}/respond', name: 'admin_contact_respond', methods: ['GET', 'POST'])]
    public function respond(Request $request, Contact $contact, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ResponseType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $response = $form->get('response')->getData();
            $contact->setReponseAdmin($response);
            $contact->setStatut(true); // Archiver le message après avoir répondu

            $email = (new Email())
                ->from('isetif149@gmail.com')
                ->to($contact->getEmail())
                ->subject('Response to your contact message')
                ->text($response);

            $mailer->send($email);

            $entityManager->flush();

            return $this->redirectToRoute('admin_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/contact/respond.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }
}
