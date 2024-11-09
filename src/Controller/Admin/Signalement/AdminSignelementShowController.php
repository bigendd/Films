<?php

namespace App\Controller\Admin\Signalement;

use App\Entity\Signalement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/signalement')]
class AdminSignelementShowController extends AbstractController
{
    #[Route('/{id}', name: 'admin_signalement_show', methods: ['GET'])]
    public function show(Signalement $signalement): Response
    {
        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        return $this->render('admin/signalement/show.html.twig', [
            'signalement' => $signalement,
            'current_route' => 'admin', 
        ]);
    }

}
