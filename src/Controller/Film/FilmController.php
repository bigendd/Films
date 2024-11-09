<?php

namespace App\Controller\Film;

use App\Entity\Favoris;
use App\Service\TmdbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController 
{
    // On garde deux services ici : pour gérer la BDD (entityManager) et pour communiquer avec l'API de films (tmdbApiService)
    private $entityManager;
    private $tmdbApiService;

    // Constructeur, c'est ici qu'on injecte les services pour pouvoir les utiliser partout dans la classe
    
    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService)
    {
        $this->entityManager = $entityManager; 
        $this->tmdbApiService = $tmdbApiService; 
    }

    // Route principale du site : quand tu vas sur '/', ça déclenche cette méthode
    #[Route('/', name: 'film_list')] 
    public function index(Request $request): Response 
    {
        // On récupère les paramètres que l'utilisateur pourrait avoir envoyés (ex. recherche de film, genre, etc.)
        $searchQuery = $request->query->get('search', ''); 
        $genreId = $request->query->get('genre', ''); 
        $type = $request->query->get('type', 'discover'); // Si rien n'est spécifié, par défaut c'est 'discover'

       

        // On va chercher la liste des genres (comédie, action, etc.) grâce au service TMDB
        $genres = $this->tmdbApiService->getGenres(); 

        // Si l'utilisateur a cherché un film en particulier
        if (!empty($searchQuery)) {
            //On fait une recherche avec l'API TMDB et on récupère les films correspondants
            $films = $this->tmdbApiService->searchMovies($searchQuery);
        } else {
            // Sinon, on affiche des films en fonction du type (ex. "upcoming", "top_rated", ou le type par défaut)
            switch ($type) {
                case 'upcoming': // Films à venir
                    $films = $this->tmdbApiService->getUpcomingMovies();
                    break;
                case 'top_rated': // Films les mieux notés
                    $films = $this->tmdbApiService->getTopRatedMovies();
                    break;
                case 'popular': // Les films populaires
                    $films = $this->tmdbApiService->getPopularMovies();
                    break;
                default:
                    // Soit on filtre par genre, soit on affiche la liste des films par défaut
                    $films = $genreId 
                        ? $this->tmdbApiService->getMovies('/discover/movie', ['with_genres' => $genreId]) 
                        : $this->tmdbApiService->getMovies('/discover/movie', []);
            }
        }

        // On choppe l'utilisateur qui est connecté (si quelqu'un est connecté)
        $user = $this->getUser(); 
        $favoriteMovies = [];

        // Si l'utilisateur est connecté, on récupère ses films favoris depuis la base de données
        if ($user) {
            $favoriteMovies = $this->entityManager->getRepository(Favoris::class)->findBy(['utilisateur' => $user]); 
        }

        // On extrait juste les IDs des films favoris (parce qu'on en a besoin plus tard dans la vue)
        $favoriteMovieIds = array_map(function ($fav) {
            return $fav->getFilmId(); 
        }, $favoriteMovies);

        // Maintenant, on balance tout ça à la vue (HTML/Twig) pour afficher les résultats de recherche et les films
        return $this->render('film/index.html.twig', [
            'films' => $films['results'], // Liste des films récupérés
            'searchQuery' => $searchQuery, // La requête de recherche si elle existe
            'genres' => $genres['genres'], // Liste des genres de films (action, comédie, etc.)
            'favoriteMovieIds' => $favoriteMovieIds, // IDs des films favoris pour l'utilisateur connecté
            'current_route' => 'film_list', // La route actuelle (pratique pour le menu actif par ex.)
        ]);
    }
}
