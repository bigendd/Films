<?php
// src/Controller/Admin/ArchivageController.php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Entity\Signalement;
use App\Entity\Bannissement;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArchivageController extends AbstractController
{
    #[Route('/admin/archive/{type}/{id}', name: 'app_archive')]
    public function archive(string $type, int $id, EntityManagerInterface $entityManager): Response
    {
        $entity = null;

        switch ($type) {
            case 'avis':
                $entity = $entityManager->getRepository(Avis::class)->find($id);
                break;
            case 'signalement':
                $entity = $entityManager->getRepository(Signalement::class)->find($id);
                break;
            case 'bannissement':
                $entity = $entityManager->getRepository(Bannissement::class)->find($id);
                break;
            case 'contact':
                $entity = $entityManager->getRepository(Contact::class)->find($id);
                break;
            default:
                throw $this->createNotFoundException('Type d\'entité non reconnu.');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Entité non trouvée.');
        }

        // Inverser le statut
        $entity->setStatut(!$entity->isStatut());
        $entityManager->flush();

        // Rediriger vers la route qui affiche les sections d'archives
        return $this->redirectToRoute('app_archive_section', ['type' => $type]);
    }

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

        return $this->render('archivage/index.html.twig', [
            'type' => $type,
            'entities' => $entities,
        ]);
    }
}
