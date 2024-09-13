<?php

namespace App\Controller\Film;

use App\Entity\Favoris;
use App\Repository\AvisRepository;
use App\Service\TmdbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FilmDetailsController extends AbstractController
{
    private $entityManager;
    private $tmdbApiService;
    private $avisRepository;

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService, AvisRepository $avisRepository)
    {
        $this->entityManager = $entityManager;
        $this->tmdbApiService = $tmdbApiService;
        $this->avisRepository = $avisRepository;
    }

    #[Route('/film/{id}', name: 'film_detail')]
    public function detail(int $id, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir les détails du film.');
            return $this->redirectToRoute('app_login', ['redirect' => $request->getRequestUri()]);
        }

        $filmDetails = $this->tmdbApiService->getMovieDetails($id);
        if (!$filmDetails) {
            throw new NotFoundHttpException('Film not found in TMDb');
        }

        $isFavorite = false;
        if ($user) {
            $favorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]);
            $isFavorite = $favorite !== null;
        }

        $avis = $this->avisRepository->findByFilmIdAndStatus($id, false);

        return $this->render('film/detail.html.twig', [
            'film' => $filmDetails,
            'isFavorite' => $isFavorite,
            'avis' => $avis,
            'current_route' => 'film_details',

        ]);
    }
}
