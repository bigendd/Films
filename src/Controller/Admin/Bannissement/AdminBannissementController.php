<?php
// src/Controller/Admin/BannissementController.php

namespace App\Controller\Admin\Bannissement;

use App\Repository\BannissementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/bannissement')]
class AdminBannissementController extends AbstractController
{
    #[Route('/', name: 'admin_bannissement_index', methods: ['GET'])]
    public function index(BannissementRepository $bannissementRepository): Response
    {
        // On récupère tous les bannissements qui ont le statut = false
        return $this->render('admin/bannissement/index.html.twig', [
            'bannissements' => $bannissementRepository->findBy(['statut' => false]), // Les bannissements à afficher
            'current_route' => 'admin', // La route actuelle pour la vue
        ]);
    }

}
