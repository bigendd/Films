<?php

namespace App\Controller\Avis;

use App\Entity\Avis;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TmdbApiService;  

class AvisNewController extends AbstractController
{
    private $entityManager;
    private $tmdbApiService; 

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService)  
    {
        $this->entityManager = $entityManager; // Initialise l'EntityManager
        $this->tmdbApiService = $tmdbApiService; // Initialise le service TMDB pour récupérer les détails des films
    }

    #[Route('/avis/new/{filmId}', name: 'avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $filmId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // Vérifie que l'utilisateur est authentifié

        // Empêche les administrateurs de soumettre des avis
        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Les administrateurs ne peuvent pas laisser des avis.'); // Message d'erreur pour les administrateurs
            return $this->redirectToRoute('film_detail', ['id' => $filmId]); // Redirige vers les détails du film
        }

        // Récupère les détails du film à partir de TMDB
        $filmDetails = $this->tmdbApiService->getMovieDetails($filmId);
        if (!$filmDetails) {
            throw $this->createNotFoundException('Le film n\'existe pas.'); // Lève une exception si le film n'existe pas
        }

        // Crée une nouvelle instance de l'avis
        $avis = new Avis();
        $avis->setTitre($filmDetails['title']); // Définit le titre de l'avis en fonction du film

        // Crée le formulaire pour l'avis
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request); // Gère la requête pour le formulaire

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setUtilisateur($this->getUser()); // Associe l'utilisateur à l'avis
            $avis->setDateDeCreation(new \DateTime()); // Définit la date de création de l'avis
            $avis->setFilmId($filmId); // Définit l'ID du film

            $this->entityManager->persist($avis); // Persiste l'avis dans la base de données
            $this->entityManager->flush(); // Enregistre les modifications

            return $this->redirectToRoute('film_detail', ['id' => $filmId]); // Redirige vers les détails du film
        }

        // Rendu du formulaire pour soumettre un nouvel avis
        return $this->render('avis/new.html.twig', [
            'avis' => $avis,
            'form' => $form->createView(), // Vue du formulaire
            'current_route' => 'formulaire', // Indique la route actuelle
        ]);
    }
}
