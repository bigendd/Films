<?php

namespace App\Controller\Admin\Signalement;

use App\Entity\Signalement; 
use App\Entity\Bannissement; 
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email; 

#[Route('/admin/signalement')] // Route de base pour ce contrôleur
class AdminSignalementNewController extends AbstractController // Contrôleur qui hérite de AbstractController
{
    private $entityManager; // Propriété pour stocker l'EntityManager
    private $mailer; // Propriété pour stocker le service de mail

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer) // Constructeur de la classe
    {
        $this->entityManager = $entityManager; // On initialise l'EntityManager
        $this->mailer = $mailer; // On initialise le service de mail
    }

    #[Route('/{id}/new', name: 'admin_signalement_new', methods: ['POST'])] // Route pour la méthode handle avec POST
    public function handle(int $id, Request $request): Response
    {
        // On cherche le signalement par ID
        $signalement = $this->entityManager->getRepository(Signalement::class)->find($id);

        if (!$signalement) {
            // Si le signalement n'est pas trouvé, on renvoie une erreur 404
            return new JsonResponse(['error' => 'Signalement non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // On récupère l'avis associé au signalement
        $avis = $signalement->getAvis();

        if (!$avis) {
            // Si l'avis n'est pas trouvé, on renvoie une erreur 404
            return new JsonResponse(['error' => 'Avis non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // On récupère l'utilisateur qui a laissé l'avis
        $utilisateurBann = $avis->getUtilisateur();

        if (!$utilisateurBann) {
            // Si l'utilisateur de l'avis n'est pas trouvé, on renvoie une erreur 404
            return new JsonResponse(['error' => 'Utilisateur de l\'avis non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // On récupère le type de bannissement depuis la requête
        $banType = $request->request->get('ban_type');

        if ($banType === '7') {
            // On crée un bannissement temporaire de 7 jours
            $bannissement = new Bannissement();
            $bannissement->setUtilisateur($utilisateurBann);
            $bannissement->setRaison('Violation des règles');
            $bannissement->setDateDeBannissement(new \DateTime());
            $bannissement->setDateFin(new \DateTime('+7 days'));
            $bannissement->setDefinitif(false);
            $bannissement->setDuree('7_jour');
            $this->entityManager->persist($bannissement);
            
            // On met à jour le statut du signalement et du bannissement
            $signalement->setStatut(1);
            $bannissement->setStatut(1);

        } elseif ($banType === 'indefinite') {
            // On crée un bannissement définitif
            $bannissement = new Bannissement();
            $bannissement->setUtilisateur($utilisateurBann);
            $bannissement->setRaison('Violation des règles');
            $bannissement->setDateDeBannissement(new \DateTime());
            $bannissement->setDateFin(null);
            $bannissement->setDefinitif(true);
            $bannissement->setDuree('definitive');
            $this->entityManager->persist($bannissement);
            
            // On met à jour le statut du signalement et du bannissement
            $signalement->setStatut(1);
            $bannissement->setStatut(1);

        } else {
            // Si aucun type de bannissement n'est spécifié, on met simplement à jour le statut du signalement
            $signalement->setStatut(1);
        }

        // On sauvegarde les changements dans la base de données
        $this->entityManager->flush();

        // On crée un email pour notifier l'utilisateur du traitement du signalement
        $email = (new Email())
            ->from('amarbelaifa8@gmail.com')
            ->to($signalement->getUtilisateur()->getEmail())
            ->subject('Réponse à votre signalement')
            ->html($this->renderView('reponse/index.html.twig', [
                'signalement' => $signalement,
            ]));

        // On envoie l'email à l'utilisateur
        $this->mailer->send($email);

        // On ajoute un message flash pour notifier que l'opération a été réussie
        $this->addFlash('success', 'Réponse envoyée, utilisateur banni et signalement archivé.');

        // On redirige vers la liste des signalements
        return $this->redirectToRoute('admin_signalement_index');
    }
}
