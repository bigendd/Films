<?php
namespace App\Controller\Favories;

use App\Entity\Avis;
use App\Entity\Favoris;
use App\Service\TmdbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriesClearController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
