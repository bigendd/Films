<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbApiService
{
    private $httpClient;
    private $apiKey;
    private $baseUrl;

    public function __construct(HttpClientInterface $httpClient, string $apiKey, string $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }
    //les films
    public function getMovies(string $chemin, array $params = []): array
    {
        $response = $this->httpClient->request('GET', $this->baseUrl . $chemin, [
            'query' => array_merge(['api_key' => $this->apiKey, 'language' => 'fr-FR'], $params),
        ]);

        return $response->toArray();
    }


    //details des films
    public function getMovieDetails(int $tmdbId): ?array
    {
        $responseDetails = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId, [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ],
        ]);

        if ($responseDetails->getStatusCode() === 404) {
            return null;
        }

        $filmDetails = $responseDetails->toArray();

        $responseVideos = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId . '/videos', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ],
        ]);

        $filmDetails['videos'] = $responseVideos->toArray()['results'];

        $responseCredits = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId . '/credits', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ],
        ]);

        $credits = $responseCredits->toArray();
        $filmDetails['cast'] = $credits['cast'];
        $filmDetails['crew'] = $credits['crew'];
        $filmDetails['directors'] = array_filter($credits['crew'], function ($member) {
            return $member['job'] === 'Director';
        });

        $responseRecommendations = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId . '/recommendations', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ],
        ]);

        $filmDetails['recommendations'] = $responseRecommendations->toArray()['results'];

        return $filmDetails;
    }
    //les genres des films
    public function getGenres(): array
    {
        $response = $this->httpClient->request('GET', $this->baseUrl . '/genre/movie/list', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ],
        ]);

        return $response->toArray();
    }
    // les mieux notés 
    public function getTopRatedMovies(): array
    {
        return $this->getMovies('/movie/top_rated', ['language' => 'fr-FR']);
    }
    //les nouveaux films
    public function getUpcomingMovies(): array
    {
        return $this->getMovies('/movie/upcoming', ['language' => 'fr-FR']);
    }
    //les films recherchés
    public function searchMovies(string $query): array
    {
        if (empty($query)) {
            return ['results' => []];
        }

        $response = $this->httpClient->request('GET', $this->baseUrl . '/search/movie', [
            'query' => [
                'api_key' => $this->apiKey,
                'query' => $query,
                'language' => 'fr-FR',
            ],
        ]);

        return $response->toArray();
    }
    
    //les films populaire
    public function getPopularMovies(): array
    {
        return $this->getMovies('/movie/popular', ['language' => 'fr-FR']);
    }
}
