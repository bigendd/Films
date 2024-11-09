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

class AdminArchivageController extends AbstractController
{
    // Route pour archiver en fonction du type d'entité (avis, signalement, etc.)
    #[Route('/admin/archive/{type}/{id}', name: 'app_archive')]
    public function archive(string $type, int $id, EntityManagerInterface $entityManager): Response
    {

        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // On commence avec une entité vide
        $entity = null;

        // On vérifie quel type d'entité on doit traiter
        switch ($type) {
            case 'avis':
                // Si c'est un avis, on va chercher l'entité correspondante
                $entity = $entityManager->getRepository(Avis::class)->find($id);
                break;
            case 'signalement':
                // Si c'est un signalement, on le récupère
                $entity = $entityManager->getRepository(Signalement::class)->find($id);
                break;
            case 'bannissement':
                // Si c'est un bannissement, on récupère l'entrée
                $entity = $entityManager->getRepository(Bannissement::class)->find($id);
                break;
            case 'contact':
                // Si c'est un contact, pareil, on le récupère
                $entity = $entityManager->getRepository(Contact::class)->find($id);
                break;
            default:
                // Si le type n'existe pas, on lance une erreur
                throw $this->createNotFoundException('Type d\'entité non reconnu.');
        }

        // Si l'entité n'existe pas, on lève une autre erreur
        if (!$entity) {
            throw $this->createNotFoundException('Entité non trouvée.');
        }

        // On inverse le statut de l'entité (par exemple, active/inactive)
        $entity->setStatut(!$entity->isStatut());
        // On enregistre les changements dans la base de données
        $entityManager->flush();

        // On redirige l'utilisateur vers la section d'archivage correspondante
        return $this->redirectToRoute('app_archive_section', ['type' => $type]);
    }
}
