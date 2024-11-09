<?php

namespace App\Controller\Admin\Contact;

use App\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contact')]
class AdminContactShowController extends AbstractController
{
    #[Route('/{id}', name: 'admin_contact_view', methods: ['GET'])]
    public function viewContact(Contact $contact): Response
    {
        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // On affiche la vue pour voir les détails d'un contact
        return $this->render('admin/contact/show.html.twig', [
            'contact' => $contact,  // Les détails du contact à afficher
            'current_route' => 'admin',  // La route actuelle pour la vue
        ]);
    }
}
