<?php
// src/Controller/Security/SecurityLoginController.php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityLoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Vérifie si l'utilisateur est déjà authentifié
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('film_list');
        }

        // Récupère la dernière erreur de connexion si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Vérifie s'il y a une erreur de connexion
        if ($error) {
            // Effacer l'email incorrect de la session
            $session = $request->getSession();
            if ($session->has('_security.last_username')) {
                $session->remove('_security.last_username');
            }

            // Réinitialise la variable $lastUsername pour qu'elle soit vide
            $lastUsername = '';
        }

        // Gère la redirection après connexion réussie
        $redirect = $request->query->get('redirect', $this->generateUrl('film_list'));

        // Génère la réponse de la page de connexion
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'redirect' => $redirect,
            'current_route' => 'formulaire',
        ]);
    }
}
