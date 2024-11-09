<?php

namespace App\Controller\Favories;

use App\Entity\Favoris;
use App\Service\TmdbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FavoriesAddController extends AbstractController 
{
    private $entityManager; // Gère les opérations sur la base de données
    private $tmdbApiService; // Pour interagir avec l'API TMDB

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService)
    {
        // On initialise les services nécessaires
        $this->entityManager = $entityManager; 
        $this->tmdbApiService = $tmdbApiService; 
    }

    #[Route('/film/{id}/favorite', name: 'film_add_favorite', methods: ['POST'])] 
    public function addFavorite(int $id): Response 
    {
        // On récupère l'utilisateur connecté
        $user = $this->getUser(); 

        // Si l'utilisateur n'est pas connecté, redirection vers la page de login
        if (!$user) {
            return $this->redirectToRoute('app_login'); 
        }

        // Vérifie si le film est déjà dans les favoris
        $existingFavorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]);
        if ($existingFavorite) {
            return $this->redirectToRoute('favorite_list'); // Redirection si déjà favori
        }

        // Récupère les détails du film via l'API TMDB
        $filmDetails = $this->tmdbApiService->getMovieDetails($id);
        if (!$filmDetails) {
            throw new NotFoundHttpException('Film not found in TMDb'); // Erreur si le film n'est pas trouvé
        }

        $posterPath = $filmDetails['poster_path']; // Récupère le chemin du poster depuis les détails du film

        // Crée un nouvel objet Favoris et y met les infos du film
        $favoris = new Favoris();
        $favoris->setTitre($filmDetails['title']); // Titre du film
        $favoris->setFilmId($id); // ID du film
        $favoris->setUtilisateur($user); // Utilisateur qui a ajouté en favori
        $favoris->setDateDeCreation(new \DateTime()); // Date de création
        $favoris->setChemin($posterPath); // Définit le chemin de l'image
        $favoris->setStatut(false);  // Met le statut à false (peut-être pour indiquer non publié)

        // Persiste et enregistre les changements dans la base de données
        $this->entityManager->persist($favoris);
        $this->entityManager->flush();

        // Redirection vers la liste des films
        return $this->redirectToRoute('film_list'); 
    }
}
