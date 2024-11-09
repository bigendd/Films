<?php

namespace App\Controller\Admin\Avis;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAvisController extends AbstractController
{
    private $entityManager;

    // On injecte l'EntityManager dans le contrôleur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/avis', name: 'admin_avis_index', methods: ['GET'])]
    public function index(): Response
    {
        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // On récupère tous les avis qui ont le statut = 0 (peut-être en attente de traitement)
        $avis = $this->entityManager->getRepository(Avis::class)->findBy(['statut' => 0]);

        // On affiche la liste des avis dans la vue
        return $this->render('admin/avis/index.html.twig', [
            'avis' => $avis,  // Les avis à afficher
            'current_route' => 'admin',  // La route actuelle pour la vue
        ]);
    }
}
