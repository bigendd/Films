<?php

namespace App\Controller\Admin\Bannissement;

use App\Entity\Bannissement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/bannissement')]
class AdminBannissementShowController extends AbstractController
{
    #[Route('/{id}', name: 'admin_bannissement_show', methods: ['GET'])]
    public function show(Bannissement $bannissement): Response
    {
        // On rend la vue pour afficher les détails du bannissement
        return $this->render('admin/bannissement/show.html.twig', [
            'bannissement' => $bannissement,  // Le bannissement à afficher
            'current_route' => 'admin',  // La route actuelle pour la vue
        ]);
    }
}
