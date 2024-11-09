<?php

namespace App\Controller\Film;

use App\Entity\Favoris;
use App\Repository\AvisRepository;
use App\Service\TmdbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FilmDetailsController extends AbstractController 
{
    private $entityManager; // Gère les opérations sur la base de données
    private $tmdbApiService; // Pour interagir avec l'API TMDB
    private $avisRepository; // Gère les avis sur les films

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService, AvisRepository $avisRepository)
    {
        // On initialise les services nécessaires
        $this->entityManager = $entityManager; 
        $this->tmdbApiService = $tmdbApiService; 
        $this->avisRepository = $avisRepository; 
    }

    #[Route('/film/{id}', name: 'film_detail')] 
    public function detail(int $id, Request $request): Response 
    {
        // On récupère l'utilisateur connecté
        $user = $this->getUser(); 

        // Vérifie si l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir les détails du film.'); // Message d'erreur
            return $this->redirectToRoute('app_login', ['redirect' => $request->getRequestUri()]); // Redirection vers la page de login
        }

        // Récupère les détails du film via l'API TMDB
        $filmDetails = $this->tmdbApiService->getMovieDetails($id); 
        if (!$filmDetails) {
            throw new NotFoundHttpException('Film not found in TMDb'); // Erreur si le film n'est pas trouvé
        }

        // Vérifie si le film est dans les favoris de l'utilisateur
        $isFavorite = false;
        if ($user) {
            $favorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]); 
            $isFavorite = $favorite !== null; // Si le film est trouvé dans les favoris
        }

        // Récupère les avis sur le film qui ne sont pas encore publiés
        $avis = $this->avisRepository->findByFilmIdAndStatus($id, false); 

        // Renvoie la vue avec les détails du film, l'état des favoris et les avis
        return $this->render('film/detail.html.twig', [
            'film' => $filmDetails, // Détails du film
            'isFavorite' => $isFavorite, // Si le film est favori ou pas
            'avis' => $avis, // Avis sur le film
            'current_route' => 'film_details', // Route actuelle
        ]);
    }
}
