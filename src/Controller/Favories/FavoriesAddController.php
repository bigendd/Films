<?php

namespace App\Controller\Favories;

use App\Entity\Favoris;
use App\Service\TmdbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FavoriesAddController extends AbstractController
{
    private $entityManager;
    private $tmdbApiService;

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService)
    {
        $this->entityManager = $entityManager;
        $this->tmdbApiService = $tmdbApiService;
    }

    #[Route('/film/{id}/favorite', name: 'film_add_favorite', methods: ['POST'])]
    public function addFavorite(int $id, Request $request): Response
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
        $posterPath = $filmDetails['poster_path']; // Récupère le chemin du poster depuis les détails du film

        if (!$filmDetails) {
            throw new NotFoundHttpException('Film not found in TMDb');
        }

        $favoris = new Favoris();
        $favoris->setTitre($filmDetails['title']);
        $favoris->setFilmId($id);
        $favoris->setUtilisateur($user);
        $favoris->setDateDeCreation(new \DateTime());
        $favoris->setChemin($posterPath); // Définit le chemin de l'image
        $favoris->setStatut(false);  // Ensure statut is set to false

        $this->entityManager->persist($favoris);
        $this->entityManager->flush();

        return $this->redirectToRoute('favorite_list');    }
}
