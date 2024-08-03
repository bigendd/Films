<?php
// src/Controller/ProfileController.php

namespace App\Controller\Profile;

use App\Entity\InfoUtilisateur;
use App\Entity\Utilisateur;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile_view', methods: ['GET'])]
    public function view(UserInterface $user, EntityManagerInterface $em): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $user;

        // Récupérer InfoUtilisateur associé
        $infoUtilisateur = $em->getRepository(InfoUtilisateur::class)->findOneBy(['utilisateur' => $utilisateur]);

        return $this->render('profile/view.html.twig', [
            'utilisateur' => $utilisateur,
            'infoUtilisateur' => $infoUtilisateur,
            'current_route' => 'formulaire', 
        ]);
    }


}
