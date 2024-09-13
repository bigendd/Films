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

#[Route('/profile')]
class ProfileEditController extends AbstractController
{

    #[Route('/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $user;

        $infoUtilisateur = $em->getRepository(InfoUtilisateur::class)->findOneBy(['utilisateur' => $utilisateur]);

        if (!$infoUtilisateur) {
            $infoUtilisateur = new InfoUtilisateur();
            $infoUtilisateur->setUtilisateur($utilisateur);
        }

        $form = $this->createForm(ProfileType::class, $infoUtilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $infoUtilisateur->setDateDeModification(new \DateTime());

            $em->persist($infoUtilisateur);
            $em->flush();

            return $this->redirectToRoute('profile_edit');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'current_route' => 'formulaire', 
        ]);
    }
}
