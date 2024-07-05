<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Repository\AvisRepository;
use App\Service\TmdbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    private $entityManager;
    private $tmdbApiService;
    private $reviewRepository;

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService, AvisRepository $reviewRepository)
    {
        $this->entityManager = $entityManager;
        $this->tmdbApiService = $tmdbApiService;
        $this->reviewRepository = $reviewRepository;
    }

    #[Route('/', name: 'film_list')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);

        if ($searchQuery) {
            $films = $this->tmdbApiService->getMovies('/search/movie', [
                'query' => $searchQuery,
                'page' => $page
            ]);
        } else {
            $films = $this->tmdbApiService->getMovies('/discover/movie', [
                'page' => $page
            ]);
        }

        $pagination = $paginator->paginate(
            $films['results'],
            $page,
            10
        );

        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = 10;
        $totalPages = ceil($totalItems / $itemsPerPage);

        return $this->render('film/index.html.twig', [
            'films' => $pagination,
            'searchQuery' => $searchQuery,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }

    #[Route('/favorites', name: 'favorite_list')]
    public function favoriteList(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $favorites = $this->entityManager->getRepository(Favoris::class)->findBy(['utilisateur' => $user]);

        return $this->render('film/favorie.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/film/{id}', name: 'film_detail')]
    public function detail(int $id): Response
    {
        $filmDetails = $this->tmdbApiService->getMovieDetails($id);

        if (!$filmDetails) {
            throw new NotFoundHttpException('Film not found in TMDb');
        }

        $user = $this->getUser();
        $isFavorite = false;

        if ($user) {
            $favorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]);
            $isFavorite = $favorite !== null;
        }

        $reviews = $this->reviewRepository->findBy(['filmId' => $id]);

        return $this->render('film/detail.html.twig', [
            'film' => $filmDetails,
            'isFavorite' => $isFavorite,
            'reviews' => $reviews,
        ]);
    }

    #[Route('/film/{id}/favorite', name: 'film_add_favorite', methods: ['POST'])]
    public function addFavorite(int $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $existingFavorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]);

        if ($existingFavorite) {
            return $this->redirectToRoute('favorite_list');
        }

        $filmDetails = $this->tmdbApiService->getMovieDetails($id);

        if (!$filmDetails) {
            throw new NotFoundHttpException('Film not found in TMDb');
        }

        $favorie = new Favoris();
        $favorie->setTitre($filmDetails['title']);
        $favorie->setFilmId($id);
        $favorie->setUtilisateur($user);
        $favorie->setDateDeCreation(new \DateTime());
        $favorie->setStatut(false);  // Ensure statut is set to false

        $this->entityManager->persist($favorie);
        $this->entityManager->flush();

        return $this->redirectToRoute('film_detail', ['id' => $id]);
    }

    #[Route('/favorites/{id}/delete', name: 'favorite_delete', methods: ['POST'])]
    public function deleteFavorite(int $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $favorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]);

        if ($favorite) {
            $this->entityManager->remove($favorite);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('favorite_list');
    }

    #[Route('/favorites/clear', name: 'favorite_clear', methods: ['POST'])]
    public function clearFavorites(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $favorites = $this->entityManager->getRepository(Favoris::class)->findBy(['utilisateur' => $user]);

        foreach ($favorites as $favorite) {
            $this->entityManager->remove($favorite);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('favorite_list');
    }
}
