<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CacheControlSubscriber implements EventSubscriberInterface
{
    // Retourne les événements auxquels ce souscripteur s'abonne
    //static ça permet de symfony d'appler directement la classe emme meme sans l'instance
    public static function getSubscribedEvents() 
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse', // S'abonne à l'événement de réponse
        ];
    }

    // Méthode appelée lors de l'événement de réponse
    public function onKernelResponse(ResponseEvent $event)
    {
        $request = $event->getRequest(); // Récupère la requête actuelle
        $route = $request->attributes->get('_route'); // Récupère la route de la requête

        // Liste des routes pour lesquelles on veut désactiver le cache
        $noCacheRoutes = ['app_login', 'app_register'];

        // Vérifie si la route actuelle est dans la liste des routes sans cache
        if (in_array($route, $noCacheRoutes)) {
            $response = $event->getResponse(); // Récupère la réponse à modifier
            // Définit les en-têtes pour désactiver le cache
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate'); // Empêche le cache
            $response->headers->set('Pragma', 'no-cache'); // Ancienne directive pour les navigateurs
            $response->headers->set('Expires', '0'); // Définit l'expiration à zéro
        }
    }
}
