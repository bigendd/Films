<?php

namespace App\Controller\Signalement;

use App\Entity\Signalement;
use App\Entity\Avis;
use App\Form\SignalementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignalementController extends AbstractController
{
    #[Route('/signalement/new/{id}', name: 'signalement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Création d'un nouveau signalement
        $signalement = new Signalement();
        // Création du formulaire pour le signalement
        $form = $this->createForm(SignalementType::class, $signalement);
        $form->handleRequest($request); // Gère la requête HTTP pour le formulaire

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère l'utilisateur actuel et l'avis associé
            $signalement->setUtilisateur($this->getUser());
            $signalement->setAvis($avis);
            $signalement->setDateDeCreation(new \DateTime()); // Définit la date de création du signalement

            // Persiste le signalement dans la base de données
            $entityManager->persist($signalement);
            $entityManager->flush();

            // Redirige vers la page de détails du film
            return $this->redirectToRoute('film_detail', ['id' => $avis->getFilmId()], Response::HTTP_SEE_OTHER);
        }

        // Rendre la vue pour créer un nouveau signalement avec le formulaire
        return $this->render('signalement/new.html.twig', [
            'form' => $form->createView(), // Passe le formulaire à la vue
            'current_route' => 'formulaire', // Passe la route actuelle à la vue
        ]);
    }
}
