<?php

namespace App\Controller\Favories;

use App\Entity\Favoris;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriesDeleteController extends AbstractController 
{
    private $entityManager; // Gère les opérations sur la base de données

    public function __construct(EntityManagerInterface $entityManager) 
    {
        // Initialisation de l'EntityManager
        $this->entityManager = $entityManager; 
    }

    #[Route('/favorite/{id}/delete', name: 'favorite_delete', methods: ['POST'])] 
    public function deleteFavorite(Request $request, int $id): Response 
    {
        // On récupère l'utilisateur connecté
        $user = $this->getUser(); 
        // Si pas de connexion, on redirige vers la page de login
        if (!$user) {
            $this->addFlash('error', 'User not authenticated.'); // Message d'erreur flash
            return $this->redirectToRoute('app_login'); 
        }

        // Vérifie le token CSRF pour éviter les attaques
        if (!$this->isCsrfTokenValid('delete_favorite_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.'); // Message d'erreur pour token invalide
            return $this->redirectToRoute('film_list'); 
        }

        // Récupère le favori à supprimer pour cet utilisateur
        $favorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]); 

        if ($favorite) {
            // Si trouvé, on le supprime
            $this->entityManager->remove($favorite);
            $this->entityManager->flush(); // Envoie les changements à la base de données
            $this->addFlash('success', 'Favorite removed successfully.'); // Message de succès
        } else {
            $this->addFlash('error', 'Favorite not found.'); // Message d'erreur si pas trouvé
        }

        // Récupère l'URL de redirection si fournie
        $redirectUrl = $request->request->get('redirect_url'); 
        if ($redirectUrl) {
            return $this->redirect($redirectUrl); // Redirection personnalisée
        }

        return $this->redirectToRoute('film_list'); // Redirection par défaut
    }
}
