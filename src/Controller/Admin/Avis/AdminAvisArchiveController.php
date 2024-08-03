<?php
// src/Controller/Admin/AdminAvisController.php

 namespace App\Controller\Admin\Avis;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class AdminAvisArchiveController extends AbstractController
{
    private $entityManager;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        
    }


   #[Route('/admin/avis{id}/archive', name: 'admin_avis_archive', methods: ['POST'])]
   public function archive(int $id, Request $request): Response
   {
       

       $avi = $this->entityManager->getRepository(Avis::class)->find($id);
       if ($avi) {
           $avi->setStatut(true);

           // Retrieve the title of the film from the Avis entity
           $filmTitle = $avi->getTitre() ?? 'le film';

           // Send email notification
           $email = (new TemplatedEmail())
               ->from('admin@yourapp.com')
               ->to($avi->getUtilisateur()->getEmail())
               ->subject('Votre avis a été archivé')
               ->htmlTemplate('reponse_archive/reponse_archive.html.twig')
               ->context([
                   'avi' => $avi,
                   'filmTitle' => $filmTitle,
               ]);

           $this->mailer->send($email);

           $this->entityManager->flush();

           $this->addFlash('notice', 'L\'avis a été archivé et l\'utilisateur a été notifié.');
       }

       return $this->redirectToRoute('film_detail', ['id' => $avi->getFilmId()]);
   }
}
