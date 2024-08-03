<?php
// src/Controller/Admin/AdminSignalementController.php

namespace App\Controller\Admin\Signalement;

use App\Entity\Signalement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/signalement')]
class AdminSignalementController extends AbstractController
{
    private $entityManager;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'admin_signalement_index', methods: ['GET'])]
    public function index(): Response
    {
        $signalements = $this->entityManager->getRepository(Signalement::class)->findBy(['statut' => 0]);

        return $this->render('admin/signalement/index.html.twig', [
            'signalements' => $signalements,
            'current_route' => 'admin', 
        ]);
    }

}