<?php
// src/Controller/SignalementController.php

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $signalement = new Signalement();
        $form = $this->createForm(SignalementType::class, $signalement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signalement->setUtilisateur($this->getUser());
            $signalement->setAvis($avis);
            $signalement->setDateDeCreation(new \DateTime());

            $entityManager->persist($signalement);
            $entityManager->flush();

            return $this->redirectToRoute('film_detail', ['id' => $avis->getFilmId()], Response::HTTP_SEE_OTHER);        }

        return $this->render('signalement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
