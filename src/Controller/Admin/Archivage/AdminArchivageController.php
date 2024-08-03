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

class AdminArchivageController extends AbstractController
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
}