<?php

namespace App\Controller\Film;

use App\Service\TmdbApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FilmAutocompliteController extends AbstractController 
{
    private $tmdbApiService; // Service pour interagir avec l'API TMDB

    public function __construct(TmdbApiService $tmdbApiService)
    {
        // On initialise le service TMDB
        $this->tmdbApiService = $tmdbApiService; 
    }

    #[Route('/autocomplete', name: 'film_autocomplete', methods: ['GET'])] 
    public function autocomplete(Request $request): JsonResponse 
    {
        // Récupère la requête de recherche
        $query = $request->query->get('query', ''); 

        // Si la requête est vide, renvoie un tableau vide
        if (empty($query)) {
            return $this->json([]); 
        }

        // Récupère les films correspondants à la requête via l'API TMDB
        $films = $this->tmdbApiService->getMovies('/search/movie', ['query' => $query]); 

        // Prépare les suggestions à renvoyer
        $suggestions = array_map(function ($film) {
            return [
                'title' => $film['title'], // Titre du film
                'id' => $film['id'] // ID du film
            ];
        }, $films['results'] ?? []); // Utilise l'opérateur de coalescence pour éviter les erreurs

        // Renvoie les données en JSON
        return $this->json($suggestions); 
    }
}
