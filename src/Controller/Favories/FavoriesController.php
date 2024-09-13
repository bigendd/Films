<?php

namespace App\Controller\Favories;

use App\Entity\Favoris;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
            'current_route' => 'film_favorie',


        ]);
    }
}
