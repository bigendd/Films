<?php

namespace App\Controller\Admin\Avis;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class AdminAvisArchiveController extends AbstractController
{
    private $entityManager;
    private $mailer;

    // On injecte l'EntityManager et le service de mailer dans le contrôleur
    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }


    #[Route('/admin/avis{id}/archive', name: 'admin_avis_archive', methods: ['POST'])]
    public function archive(int $id, Request $request): Response
    {
        // On récupère l'avis par son id
        $avi = $this->entityManager->getRepository(Avis::class)->find($id);
        if ($avi) {
            // On met à jour le statut de l'avis (on l'archive)
            $avi->setStatut(true);

            // On récupère le titre du film ou un titre par défaut si y'en a pas
            $filmTitle = $avi->getTitre() ?? 'le film';

            // On prépare un email pour notifier l'utilisateur que son avis a été archivé
            $email = (new TemplatedEmail())
                ->from('amarbelaifa8@gmail.com')  // Adresse expéditeur
                ->to($avi->getUtilisateur()->getEmail())  // Email de l'utilisateur
                ->subject('Votre avis a été archivé')  // Sujet de l'email
                ->htmlTemplate('reponse_archive/reponse_archive.html.twig')  // Template pour le corps de l'email
                ->context([
                    'avi' => $avi,
                    'filmTitle' => $filmTitle,  // On passe le titre du film dans le contexte de l'email
                ]);

            // Envoie de l'email à l'utilisateur
            $this->mailer->send($email);

            // On enregistre les changements dans la base de données
            $this->entityManager->flush();

            // On ajoute un message flash pour indiquer que l'archivage a bien eu lieu
            $this->addFlash('notice', 'L\'avis a été archivé et l\'utilisateur a été notifié.');
        }

        // On redirige vers la page de détails du film après l'archivage
        return $this->redirectToRoute('film_detail', ['id' => $avi->getFilmId()]);
    }
}
