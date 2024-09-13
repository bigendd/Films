<?php
// src/Service/TmdbApiService.php

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

    public function getMovies(string $endpoint, array $params = []): array
    {
        $response = $this->httpClient->request('GET', $this->baseUrl . $endpoint, [
            'query' => array_merge(['api_key' => $this->apiKey, 'language' => 'fr-FR'], $params),
        ]);

        return $response->toArray();
    }

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

    // Nouveaux services ajoutés
    public function getTopRatedMovies(): array
    {
        return $this->getMovies('/movie/top_rated');
    }

    public function getUpcomingMovies(): array
    {
        return $this->getMovies('/movie/upcoming');
    }

    public function searchMovies(string $query): array
    {
        return $this->getMovies('/search/movie', ['query' => $query]);
    }

    public function getPopularMovies(): array
    {
        return $this->getMovies('/movie/popular');
    }

    public function getMovieReviews(int $tmdbId): array
    {
        $response = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId . '/reviews', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'fr-FR',
            ],
        ]);

        return $response->toArray();
    }
}
