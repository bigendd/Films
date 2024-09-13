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
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/favorite/{id}/delete', name: 'favorite_delete', methods: ['POST'])]
    public function deleteFavorite(Request $request, int $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'User not authenticated.');
            return $this->redirectToRoute('app_login');
        }

        // Check CSRF token
        if (!$this->isCsrfTokenValid('delete_favorite_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('film_list');
        }

        $favorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]);

        if ($favorite) {
            $this->entityManager->remove($favorite);
            $this->entityManager->flush();
            $this->addFlash('success', 'Favorite removed successfully.');
        } else {
            $this->addFlash('error', 'Favorite not found.');
        }

        $redirectUrl = $request->request->get('redirect_url');
        if ($redirectUrl) {
            return $this->redirect($redirectUrl);
        }

        return $this->redirectToRoute('film_list');
    }
}
