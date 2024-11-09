<?php

namespace App\Controller\Profile;

use App\Entity\InfoUtilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile_view', methods: ['GET'])]
    public function view(UserInterface $user, EntityManagerInterface $em): Response
    {
        // Récupération de l'utilisateur authentifié
        $utilisateur = $user;

        // Recherche des informations de l'utilisateur dans la base de données
        $infoUtilisateur = $em->getRepository(InfoUtilisateur::class)->findOneBy(['utilisateur' => $utilisateur]);

        // Rendu de la vue avec les informations de l'utilisateur
        return $this->render('profile/view.html.twig', [
            'utilisateur' => $utilisateur, // Passer l'utilisateur à la vue
            'infoUtilisateur' => $infoUtilisateur, // Passer les informations de l'utilisateur à la vue
            'current_route' => 'formulaire', // Passer la route actuelle à la vue
        ]);
    }
}
