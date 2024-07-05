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
        $response = $this->httpClient->request('GET', $this->baseUrl . '/movie/' . $tmdbId, [
            'query' => ['api_key' => $this->apiKey],
        ]);

        if ($response->getStatusCode() === 404) {
            return null;
        }

        return $response->toArray();
    }
}
