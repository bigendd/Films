<?php
// src/Controller/Admin/AdminContactController.php

namespace App\Controller\Admin\Contact;

use App\Entity\Contact;
use App\Form\ReponseType;
use App\Repository\ContactRepository;
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

        $form = $this->createForm(ReponseType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse = $form->get('reponse')->getData();
            $contact->setReponseAdmin($reponse);
            $contact->setStatut(true); // Archiver le message après avoir répondu

            $email = (new Email())
                ->from('isetif149@gmail.com')
                ->to($contact->getEmail())
                ->subject('Réponse a votre message')
                ->text($reponse);

            $mailer->send($email);

            $entityManager->flush();

            return $this->redirectToRoute('admin_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/contact/respond.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'current_route' => 'admin', 
        ]);
    }
}
