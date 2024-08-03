<?php
namespace App\Controller\Film;

use App\Service\TmdbApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class FilmAutocompliteController extends AbstractController
{
    private $tmdbApiService;

    public function __construct(TmdbApiService $tmdbApiService)
    {
        $this->tmdbApiService = $tmdbApiService;
    }

    #[Route('/autocomplete', name: 'film_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('query', '');
        
        if (empty($query)) {
            return $this->json([]);
        }
        
        $films = $this->tmdbApiService->getMovies('/search/movie', ['query' => $query]);
        
        $suggestions = array_map(function ($film) {
            return [
                'title' => $film['title'],
                'id' => $film['id']
            ];
        }, $films['results'] ?? []);
        
        return $this->json($suggestions);
    }
}
