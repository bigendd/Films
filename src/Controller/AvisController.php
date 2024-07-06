<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\FilmRepository;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/review/new/{filmId}', name: 'review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $filmId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $review = new Avis();
        $form = $this->createForm(AvisType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setUtilisateur($this->getUser());
            $review->setDateDeCreation(new \DateTime());
            $review->setFilmId($filmId);

            $this->entityManager->persist($review);
            $this->entityManager->flush();

            return $this->redirectToRoute('film_detail', ['id' => $filmId]);
        }

        return $this->render('avis/new.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/review/{id}/edit', name: 'review_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $review): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser() !== $review->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AvisType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('film_detail', ['id' => $review->getFilmId()]);
        }

        return $this->render('avis/edit.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/review/{id}/delete', name: 'review_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $review): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser() !== $review->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($review);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('film_detail', ['id' => $review->getFilmId()]);
    }

  

   
}
