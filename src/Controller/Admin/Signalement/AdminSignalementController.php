<?php

namespace App\Controller\Admin\Signalement;

use App\Entity\Signalement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/signalement')]
class AdminSignalementController extends AbstractController
{
    private $entityManager;

    // On injecte le gestionnaire d'entités pour pouvoir interagir avec la base de données
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'admin_signalement_index', methods: ['GET'])]
    public function index(): Response
    {
        // On récupère tous les signalements qui ne sont pas encore traités (statut = 0)
        $signalements = $this->entityManager->getRepository(Signalement::class)->findBy(['statut' => 0]);

        // On rend la vue pour afficher la liste des signalements
        return $this->render('admin/signalement/index.html.twig', [
            'signalements' => $signalements,  // Les signalements à afficher
            'current_route' => 'admin',  // La route actuelle pour la vue
        ]);
    }
}
