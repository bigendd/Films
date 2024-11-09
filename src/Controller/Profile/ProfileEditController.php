<?php

namespace App\Controller\Profile;

use App\Entity\InfoUtilisateur; 
use App\Form\ProfileType; 
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Component\Security\Core\User\UserInterface; 

#[Route('/profile')] // Définit la route de base pour le contrôleur
class ProfileEditController extends AbstractController
{
    #[Route('/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserInterface $utilisateur): Response
    {
        // Récupère les informations de l'utilisateur à partir de la base de données
        $infoUtilisateur = $entityManager->getRepository(InfoUtilisateur::class)->findOneBy(['utilisateur' => $utilisateur]);

        // Si aucune information n'est trouvée, crée une nouvelle instance d'InfoUtilisateur
        if (!$infoUtilisateur) {
            $infoUtilisateur = new InfoUtilisateur();
            $infoUtilisateur->setUtilisateur($utilisateur); // Associe l'utilisateur à l'instance
        }

        // Crée le formulaire pour éditer le profil
        $form = $this->createForm(ProfileType::class, $infoUtilisateur);
        $form->handleRequest($request); // Gère la requête HTTP pour le formulaire

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Met à jour la date de modification
            $infoUtilisateur->setDateDeModification(new \DateTime());

            // Persist les données dans la base de données
            $entityManager->persist($infoUtilisateur);
            $entityManager->flush(); // Sauvegarde les modifications

            // Redirige vers la vue du profil après la modification
            return $this->redirectToRoute('profile_view');
        }

        // Rend la vue avec le formulaire
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(), // Passe le formulaire à la vue
            'current_route' => 'formulaire', // Passe la route actuelle à la vue
        ]);
    }
}
