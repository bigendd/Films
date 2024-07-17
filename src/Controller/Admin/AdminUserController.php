<?php
// src/Controller/UserController.php

namespace App\Controller\Admin;

use App\Repository\BannissementRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_users')]
    public function index(UtilisateurRepository $userRepository, BannissementRepository $banissementRepository): Response
    {
        $users = $userRepository->findAll();

        // Créez un tableau associatif pour lier les utilisateurs à leur statut de bannissement
        $usersWithBanStatus = [];
        foreach ($users as $user) {
            $ban = $banissementRepository->findOneBy(['utilisateur' => $user]);
            $usersWithBanStatus[] = [
                'user' => $user,
                'isBanned' => $ban ? $ban->isStatut() : false,
            ];
        }

        return $this->render('admin_user/index.html.twig', [
            'users' => $usersWithBanStatus,
        ]);
    }
}
