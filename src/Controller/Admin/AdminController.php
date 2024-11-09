<?php
// src/Controller/AdminController.php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    

    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Logique pour afficher l'archive
        return $this->render('admin/index.html.twig', [
        
            'current_route' => 'admin', 
            
            
        ]);
    }

}
