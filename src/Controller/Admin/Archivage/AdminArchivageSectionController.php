<?php

namespace App\Controller\Admin\Archivage;

use App\Entity\Avis;
use App\Entity\Signalement;
use App\Entity\Bannissement;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArchivageSectionController extends AbstractController
{
    // Route pour afficher les entités archivées en fonction du type
    #[Route('/admin/archive/{type}', name: 'app_archive_section')]
    public function archiveSection(string $type, EntityManagerInterface $entityManager): Response
    {
        // On commence avec un repository vide
        $repository = null;

        // On vérifie le type d'entité pour récupérer le bon repository
        switch ($type) {
            case 'avis':
                // Si c'est un avis, on récupère son repository
                $repository = $entityManager->getRepository(Avis::class);
                break;
            case 'signalement':
                // Si c'est un signalement, on prend son repository
                $repository = $entityManager->getRepository(Signalement::class);
                break;
            case 'bannissement':
                // Si c'est un bannissement, pareil on récupère le repository
                $repository = $entityManager->getRepository(Bannissement::class);
                break;
            case 'contact':
                // Si c'est un contact, on récupère le bon repository
                $repository = $entityManager->getRepository(Contact::class);
                break;
            default:
                // Si le type n'est pas reconnu, on lève une erreur
                throw $this->createNotFoundException('Type d\'entité non reconnu.');
        }

        // On récupère seulement les entités qui ont un statut à true (archivées par ex.)
        $entities = $repository->findBy(['statut' => true]);

        // On passe tout ça à la vue pour les afficher
        return $this->render('admin/archivage/index.html.twig', [
            'type' => $type, // Le type d'entité
            'entities' => $entities, // Les entités archivées
            'current_route' => 'admin', // La route active pour la vue
        ]);
    }
}
