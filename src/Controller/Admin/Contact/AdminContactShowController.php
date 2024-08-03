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
class AdminContactShowController extends AbstractController
{
    #[Route('/admin/contact/{id}', name: 'admin_contact_view', methods: ['GET'])]
   
   public function viewContact(Contact $contact): Response
   {
       return $this->render('admin/contact/show.html.twig', [
           'contact' => $contact,
           'current_route' => 'admin',
       ]);
   }
   

}