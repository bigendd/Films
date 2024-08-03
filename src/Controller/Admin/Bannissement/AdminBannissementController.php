<?php
// src/Controller/Admin/BannissementController.php

namespace App\Controller\Admin\Bannissement;

use App\Entity\Bannissement;
use App\Form\BannissementType;
use App\Repository\BannissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/bannissement')]
class AdminBannissementController extends AbstractController
{
    #[Route('/', name: 'admin_bannissement_index', methods: ['GET'])]
    public function index(BannissementRepository $bannissementRepository): Response
    {
        return $this->render('admin/bannissement/index.html.twig', [
            'bannissements' => $bannissementRepository->findBy(['statut' => false]),
            'current_route' => 'admin', 
        ]);
    }

}
