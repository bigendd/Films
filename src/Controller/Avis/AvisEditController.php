<?php

namespace App\Controller\Avis;

use App\Entity\Avis;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisEditController extends AbstractController 
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)  
    {
        $this->entityManager = $entityManager; // Initialise l'EntityManager
    }

    #[Route('/avis/{id}/edit', name: 'avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avis): Response 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // Vérifie que l'utilisateur est authentifié

        // Vérifie si l'utilisateur courant est le propriétaire de l'avis
        if ($this->getUser() !== $avis->getUtilisateur()) {
            throw $this->createAccessDeniedException(); // Lève une exception si l'accès est refusé
        }

        // Crée le formulaire avec l'entité Avis
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request); // Gère la requête pour le formulaire

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush(); // Enregistre les modifications dans la base de données

            return $this->redirectToRoute('film_detail', ['id' => $avis->getFilmId()]); // Redirige vers les détails du film
        }

        // Rendu du formulaire avec les données actuelles de l'avis
        return $this->render('avis/edit.html.twig', [
            'avis' => $avis,
            'form' => $form->createView(),
            'current_route' => 'formulaire', // Passer la route actuelle à la vue
        ]);
    }
}
