<?php
// src/Controller/AdminController.php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/reports', name: 'admin_reports')]
    public function reports(): Response
    {
        // Logique pour afficher les signalements
        return $this->render('admin/reports.html.twig');
    }

    #[Route('/admin/bans', name: 'admin_bans')]
    public function bans(): Response
    {
        // Logique pour gérer les bannissements
        return $this->render('admin/bans.html.twig');
    }

    #[Route('/admin/archive', name: 'admin_archive')]
    public function archive(): Response
    {
        // Logique pour afficher l'archive
        return $this->render('archivage/index.html.twig');
    }
}
