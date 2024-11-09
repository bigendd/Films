<?php

namespace App\Controller\Favories;

use App\Entity\Favoris;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriesController extends AbstractController 
{
    private $entityManager; // Gère les interactions avec la base de données

    public function __construct(EntityManagerInterface $entityManager) 
    {
        // On initialise l'EntityManager
        $this->entityManager = $entityManager; 
    }

    #[Route('/favorites', name: 'favorite_list')] 
    public function favoriteList(): Response 
    {
        // On récupère l'utilisateur connecté
        $user = $this->getUser(); 

        // Si l'utilisateur n'est pas connecté, redirection vers la page de login
        if (!$user) {
            return $this->redirectToRoute('app_login'); 
        }

        // On récupère tous les favoris de l'utilisateur
        $favorites = $this->entityManager->getRepository(Favoris::class)->findBy(['utilisateur' => $user]); 

        // On rend la vue avec les favoris
        return $this->render('film/favorie.html.twig', [
            'favorites' => $favorites, // Passe les favoris à la vue
            'current_route' => 'film_favorie', // Indique la route actuelle
        ]);
    }
}
