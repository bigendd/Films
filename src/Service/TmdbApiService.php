<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbApiService
{
    private $httpClient; // Représente l'instance du client HTTP utilisé pour effectuer des requêtes API.
    private $apiKey; // Clé d'API utilisée pour accéder à l'API TMDb.
    private $baseUrl; // URL de base de l'API TMDb.

    // Le constructeur permet d'initialiser les variables nécessaires pour la communication avec l'API TMDb.
    public function __construct(HttpClientInterface $httpClient, string $apiKey, string $baseUrl)
    {
        $this->httpClient = $httpClient; // Injection du client HTTP pour effectuer les requêtes.
        $this->apiKey = $apiKey; // Initialisation de la clé API.
        $this->baseUrl = $baseUrl; // Initialisation de l'URL de base de l'API TMDb.
    }

    // Méthode pour obtenir une liste de films à partir d'un chemin donné et d'éventuels paramètres supplémentaires.
    public function getMovies(string $chemin, array $params = []): array
    {
        $response = $this->httpClient->request('GET', $this->baseUrl . $chemin, [
            'query' => array_merge(['api_key' => $this->apiKey, 'language' => 'fr-FR'], $params),
        ]);

        // Retourne la réponse de l'API sous forme de tableau.
        return $response->toArray();
    }

    // Méthode pour obtenir les détails d'un film spécifique en utilisant son ID TMDb.
    public function getMovieDetails(int $tmdbId): ?array
    {
        // Requête pour obtenir les détails du film (comme la description, la durée, etc.).
        $responseDetails = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId, [
            'query' => [
                'api_key' => $this->apiKey, // Clé API pour l'authentification.
                'language' => 'fr-FR', // Langue des données retournées.
            ],
        ]);

        // Si le film n'est pas trouvé (code 404), retourne null.
        if ($responseDetails->getStatusCode() === 404) {
            return null;
        }

        // Récupère les détails du film sous forme de tableau.
        $filmDetails = $responseDetails->toArray();

        // Requête pour obtenir les vidéos associées au film.
        $responseVideos = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId . '/videos', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ],
        ]);

        // Ajoute les vidéos dans les détails du film.
        $filmDetails['videos'] = $responseVideos->toArray()['results'];

        // Requête pour obtenir les crédits du film (acteurs, équipe technique, etc.).
        $responseCredits = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId . '/credits', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ],
        ]);

        // Récupère les crédits sous forme de tableau et ajoute les informations à `filmDetails`.
        $credits = $responseCredits->toArray();
        $filmDetails['cast'] = $credits['cast']; // Liste des acteurs.
        $filmDetails['crew'] = $credits['crew']; // Liste des membres de l'équipe technique.
        
        // Filtre les réalisateurs parmi l'équipe technique.
        $filmDetails['directors'] = array_filter($credits['crew'], function ($member) {
            return $member['job'] === 'Director'; // On sélectionne uniquement ceux dont le job est 'Director'.
        });

        // Requête pour obtenir des recommandations de films similaires.
        $responseRecommendations = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId . '/recommendations', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ],
        ]);

        // Ajoute les films recommandés aux détails du film.
        $filmDetails['recommendations'] = $responseRecommendations->toArray()['results'];

        // Retourne les détails complets du film, y compris les vidéos, crédits et recommandations.
        return $filmDetails;
    }

    // Méthode pour obtenir la liste des genres de films disponibles.
    public function getGenres(): array
    {
        $response = $this->httpClient->request('GET', $this->baseUrl . '/genre/movie/list', [
            'query' => [
                'api_key' => $this->apiKey, // Clé API pour authentification.
                'language' => 'fr-FR', // Langue des données retournées.
            ],
        ]);

        // Retourne la liste des genres sous forme de tableau.
        return $response->toArray();
    }

    // Méthode pour obtenir les films les mieux notés.
    public function getTopRatedMovies(): array
    {
        // Utilise la méthode `getMovies` avec le chemin approprié pour obtenir les films les mieux notés.
        return $this->getMovies('/movie/top_rated', ['language' => 'fr-FR']);
    }

    // Méthode pour obtenir les films à venir.
    public function getUpcomingMovies(): array
    {
        // Utilise la méthode `getMovies` avec le chemin approprié pour obtenir les films à venir.
        return $this->getMovies('/movie/upcoming', ['language' => 'fr-FR']);
    }

    // Méthode pour rechercher des films en fonction d'une requête donnée.
    public function searchMovies(string $query): array
    {
        // Si la requête est vide, retourne un tableau vide.
        if (empty($query)) {
            return ['results' => []];
        }

        // Requête pour rechercher des films en fonction de la chaîne de recherche.
        $response = $this->httpClient->request('GET', $this->baseUrl . '/search/movie', [
            'query' => [
                'api_key' => $this->apiKey,
                'query' => $query, // La chaîne de recherche.
                'language' => 'fr-FR', // Langue des résultats.
            ],
        ]);

        // Retourne les résultats de la recherche sous forme de tableau.
        return $response->toArray();
    }

    // Méthode pour obtenir les films populaires.
    public function getPopularMovies(): array
    {
        // Utilise la méthode `getMovies` avec le chemin approprié pour obtenir les films populaires.
        return $this->getMovies('/movie/popular', ['language' => 'fr-FR']);
    }
}
