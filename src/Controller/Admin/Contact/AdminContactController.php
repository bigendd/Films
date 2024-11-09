<?php

namespace App\Controller\Admin\Contact;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contact')]
class AdminContactController extends AbstractController
{
    #[Route('/', name: 'admin_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // On récupère les contacts qui ne sont pas archivés (statut = 0)
        $contacts = $contactRepository->findBy(['statut' => 0]);

        // On rend la vue pour afficher la liste des contacts
        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contacts,  // Les contacts à afficher
            'current_route' => 'admin',  // La route actuelle pour la vue
        ]);
    }
}
