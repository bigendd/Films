<?php
namespace App\Controller\Favories;

use App\Entity\Favoris;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriesClearController extends AbstractController 
{
    private $entityManager; // Gère les opérations sur la base de données

    public function __construct(EntityManagerInterface $entityManager)
    {
        // On initialise l'EntityManager
        $this->entityManager = $entityManager; 
    }

    #[Route('/favorites/clear', name: 'favorite_clear', methods: ['POST'])] 
    public function clearFavorites(): Response 
    {
        // On récupère l'utilisateur connecté
        $user = $this->getUser(); 

        // Si l'utilisateur n'est pas connecté, redirection vers la page de login
        if (!$user) {
            return $this->redirectToRoute('app_login'); 
        }

        // On récupère tous les favoris de l'utilisateur
        $favorites = $this->entityManager->getRepository(Favoris::class)->findBy(['utilisateur' => $user]);

        // On parcourt chaque favori et on le supprime
        foreach ($favorites as $favorite) {
            $this->entityManager->remove($favorite); // Supprime chaque favori
        }

        // On enregistre les changements dans la base de données
        $this->entityManager->flush(); 

        // Redirection vers la liste des favoris après la suppression
        return $this->redirectToRoute('favorite_list'); 
    }
}
