<?php

namespace App\Controller\Film;

use App\Entity\Avis;
use App\Entity\Favoris;
use App\Repository\AvisRepository;
use App\Service\TmdbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    private $entityManager;
    private $tmdbApiService;
    private $avisRepository;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService, AvisRepository $avisRepository, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->tmdbApiService = $tmdbApiService;
        $this->avisRepository = $avisRepository;
        $this->mailer = $mailer;
    }

    #[Route('/', name: 'film_list')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->query->get('search', '');
        $genreId = $request->query->get('genre', '');
        $type = $request->query->get('type', 'discover');
        
        $genres = $this->tmdbApiService->getGenres();

        switch ($type) {
            case 'upcoming':
                $films = $this->tmdbApiService->getUpcomingMovies();
                break;
            case 'top_rated':
                $films = $this->tmdbApiService->getTopRatedMovies();
                break;
            case 'popular':
                $films = $this->tmdbApiService->getPopularMovies();
                break;
            case 'search':
                $films = $this->tmdbApiService->searchMovies($searchQuery);
                break;
            default:
                $films = $genreId 
                    ? $this->tmdbApiService->getMovies('/discover/movie', ['with_genres' => $genreId])
                    : $this->tmdbApiService->getMovies('/discover/movie', []);
        }

        if (!isset($films['results']) || empty($films['results'])) {
            return $this->render('film/index.html.twig', [
                'films' => [],
                'searchQuery' => $searchQuery,
                'genres' => $genres['genres'],
                'current_route' => 'film_list',
            ]);
        }

        // Ensure we paginate the films correctly with a limit of 20 items per page
        $pagination = $paginator->paginate(
            $films['results'], // Query or array
            $request->query->getInt('page', 1), // Current page number
            20, // Limit per page
            ['pageLimit' => 10] // Maximum number of pages
        );

        $user = $this->getUser();

        $favoriteMovies = [];
        if ($user) {
            $favoriteMovies = $this->entityManager->getRepository(Favoris::class)->findBy(['utilisateur' => $user]);
        }

        $favoriteMovieIds = array_map(function($fav) {
            return $fav->getFilmId();
        }, $favoriteMovies);

        return $this->render('film/index.html.twig', [
            'films' => $pagination,
            'searchQuery' => $searchQuery,
            'genres' => $genres['genres'],
            'favoriteMovieIds' => $favoriteMovieIds,
            'current_route' => 'film_list',
            'debug' => [
                'totalItems' => count($films['results']),
                'apiResponse' => json_encode($films)
            ]
        ]);
    }
}
