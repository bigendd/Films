<?php
// src/Controller/Admin/AdminContactController.php

namespace App\Controller\Admin\Contact;

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

        // Récupérer uniquement les contacts non archivés (statut = 0)
        $contacts = $contactRepository->findBy(['statut' => 0]);

        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contacts,
            'current_route' => 'admin', 
        ]);
    }

}