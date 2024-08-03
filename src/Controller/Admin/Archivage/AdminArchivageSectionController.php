<?php
// src/Controller/Admin/ArchivageController.php

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

    #[Route('/admin/archive/{type}', name: 'app_archive_section')]
    public function archiveSection(string $type, EntityManagerInterface $entityManager): Response
    {
        $repository = null;

        switch ($type) {
            case 'avis':
                $repository = $entityManager->getRepository(Avis::class);
                break;
            case 'signalement':
                $repository = $entityManager->getRepository(Signalement::class);
                break;
            case 'bannissement':
                $repository = $entityManager->getRepository(Bannissement::class);
                break;
            case 'contact':
                $repository = $entityManager->getRepository(Contact::class);
                break;
            default:
                throw $this->createNotFoundException('Type d\'entité non reconnu.');
        }

        // Récupérer uniquement les entités avec statut = 1
        $entities = $repository->findBy(['statut' => true]);

        return $this->render('admin/archivage/index.html.twig', [
            'type' => $type,
            'entities' => $entities,
            'current_route' => 'admin', 
        ]);
    }
}
