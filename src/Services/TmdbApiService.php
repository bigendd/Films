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
            'query' => array_merge(['api_key' => $this->apiKey], $params),
        ]);

        return $response->toArray();
    }

    public function getMovieDetails(int $tmdbId): ?array
{
    // Récupération des détails du film
    $responseDetails = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId, [
        'query' => [
            'api_key' => $this->apiKey,
        ],
    ]);

    if ($responseDetails->getStatusCode() === 404) {
        return null;
    }

    $filmDetails = $responseDetails->toArray();

    // Récupération des vidéos de bande-annonce
    $responseVideos = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId . '/videos', [
        'query' => [
            'api_key' => $this->apiKey,
        ],
    ]);

    if ($responseVideos->getStatusCode() === 404) {
        $filmDetails['videos'] = null;
    } else {
        $videos = $responseVideos->toArray();
        $filmDetails['videos'] = $videos['results'];
    }

    return $filmDetails;
}

}
