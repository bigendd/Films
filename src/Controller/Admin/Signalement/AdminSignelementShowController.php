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
class AdminSignelementShowController extends AbstractController
{
    #[Route('/{id}', name: 'admin_signalement_show', methods: ['GET'])]
    public function show(Signalement $signalement): Response
    {
        return $this->render('admin/signalement/show.html.twig', [
            'signalement' => $signalement,
            'current_route' => 'admin', 
        ]);
    }

}
