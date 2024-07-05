<?php

namespace App\Controller;

use App\Entity\Bannissement;
use App\Form\BannissementType;
use App\Repository\BannissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/bannissement')]
class BannissementController extends AbstractController
{
    #[Route('/', name: 'app_bannissement_index', methods: ['GET'])]
    public function index(BannissementRepository $bannissementRepository): Response
    {
        return $this->render('bannissement/index.html.twig', [
            'bannissements' => $bannissementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_bannissement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bannissement = new Bannissement();
        $form = $this->createForm(BannissementType::class, $bannissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($bannissement);
            $entityManager->flush();

            return $this->redirectToRoute('app_bannissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bannissement/new.html.twig', [
            'bannissement' => $bannissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bannissement_show', methods: ['GET'])]
    public function show(Bannissement $bannissement): Response
    {
        return $this->render('bannissement/show.html.twig', [
            'bannissement' => $bannissement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_bannissement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bannissement $bannissement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BannissementType::class, $bannissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_bannissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bannissement/edit.html.twig', [
            'bannissement' => $bannissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bannissement_delete', methods: ['POST'])]
    public function delete(Request $request, Bannissement $bannissement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bannissement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($bannissement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_bannissement_index', [], Response::HTTP_SEE_OTHER);
    }
}
