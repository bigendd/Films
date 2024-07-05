<?php
// src/Controller/ProfileController.php

namespace App\Controller;

use App\Entity\InfoUtilisateur;
use App\Entity\Utilisateur;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile_view', methods: ['GET'])]
    public function view(UserInterface $user, EntityManagerInterface $em): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $user;

        // Récupérer InfoUtilisateur associé
        $infoUtilisateur = $em->getRepository(InfoUtilisateur::class)->findOneBy(['utilisateur' => $utilisateur]);

        return $this->render('profile/view.html.twig', [
            'utilisateur' => $utilisateur,
            'infoUtilisateur' => $infoUtilisateur,
        ]);
    }

    #[Route('/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $user;

        // Récupérer InfoUtilisateur associé
        $infoUtilisateur = $em->getRepository(InfoUtilisateur::class)->findOneBy(['utilisateur' => $utilisateur]);

        $form = $this->createForm(ProfileType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour les champs de InfoUtilisateur manuellement
            $infoUtilisateur->setNom($form->get('nom')->getData());
            $infoUtilisateur->setDateDeNaissance($form->get('dateDeNaissance')->getData());
            $infoUtilisateur->setGenre($form->get('genre')->getData());
            $infoUtilisateur->setAdresse($form->get('adresse')->getData());
            $infoUtilisateur->setNumeroDeTelephone($form->get('numeroDeTelephone')->getData());

            $em->persist($infoUtilisateur);
            $em->persist($utilisateur);
            $em->flush();

            return $this->redirectToRoute('profile_edit');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
