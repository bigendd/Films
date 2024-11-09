<?php

namespace App\Controller\Admin\Avis;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAvisShowController extends AbstractController
{
    private $entityManager;

    // On injecte l'EntityManager dans le contrôleur pour pouvoir accéder aux données
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/avis/{id}', name: 'admin_avis_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // On cherche l'avis avec l'id donné
        $avis = $this->entityManager->getRepository(Avis::class)->find($id);

        // Si l'avis n'existe pas, on lance une erreur
        if (!$avis) {
            throw $this->createNotFoundException('Avis non trouvé');
        }

        // On rend la vue pour afficher les détails de l'avis
        return $this->render('admin/avis/show.html.twig', [
            'avis' => $avis,  // Les détails de l'avis à afficher
            'current_route' => 'admin',  // La route actuelle pour la vue
        ]);
    }
}
