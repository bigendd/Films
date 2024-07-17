<?php
// src/Controller/ReponseController.php

namespace App\Controller;

use App\Entity\Signalement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reponse')]
class ReponseController extends AbstractController
{
    #[Route('/{id}', name: 'reponse_signalement', methods: ['POST'])]
    public function repondre(Signalement $signalement, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $email = (new TemplatedEmail())
            ->from('amarbelaifa8@gmail.com')
            ->to($signalement->getUtilisateur()->getEmail())
            ->subject('Réponse à votre signalement')
            ->htmlTemplate('reponse/index.html.twig')
            ->context([
                'signalement' => $signalement,
            ]);

        $mailer->send($email);

        // Mettre à jour le statut du signalement après l'envoi de l'email
        $signalement->setStatut(true);
        $entityManager->flush();

        return $this->redirectToRoute('admin_signalement_index');
    }
}
