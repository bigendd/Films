<?php
namespace App\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface // Déclaration de la classe qui implémente AuthenticationSuccessHandlerInterface
{
    private $router; // Déclaration d'une variable pour le routeur

    // Constructeur pour initialiser le routeur
    public function __construct(RouterInterface $router)
    {
        $this->router = $router; // Initialisation du routeur
    }

    // Méthode appelée lors d'un succès d'authentification
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        // Récupération de l'URL de redirection depuis la requête ou par défaut vers la liste des films
        $redirect = $request->get('redirect', $this->router->generate('film_list'));
        return new RedirectResponse($redirect); // Retourne une réponse de redirection vers l'URL spécifiée
    }
}
