<?php
// src/Controller/Admin/BannissementController.php

namespace App\Controller\Admin;

use App\Entity\Bannissement;
use App\Form\BannissementType;
use App\Repository\BannissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/bannissement')]
class BannissementController extends AbstractController
{
    #[Route('/', name: 'admin_bannissement_index', methods: ['GET'])]
    public function index(BannissementRepository $bannissementRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/bannissement/index.html.twig', [
            'bannissements' => $bannissementRepository->findBy(['statut' => false]),
        ]);
    }

    #[Route('/new', name: 'admin_bannissement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $bannissement = new Bannissement();
        $form = $this->createForm(BannissementType::class, $bannissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $duree = $form->get('duree')->getData();
            if ($duree === '7_jours') {
                $bannissement->setDateFin((new \DateTime())->modify('+7 days'));
                $bannissement->setDefinitif(false);
            } elseif ($duree === 'definitif') {
                $bannissement->setDefinitif(true);
                $bannissement->setDateFin(null);
                $bannissement->setStatut(true); // Automatically archive if definitive
            }

            $entityManager->persist($bannissement);
            $entityManager->flush();

            return $this->redirectToRoute('admin_bannissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/bannissement/new.html.twig', [
            'bannissement' => $bannissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_bannissement_show', methods: ['GET'])]
    public function show(Bannissement $bannissement): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/bannissement/show.html.twig', [
            'bannissement' => $bannissement,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_bannissement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bannissement $bannissement, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(BannissementType::class, $bannissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_bannissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/bannissement/edit.html.twig', [
            'bannissement' => $bannissement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_bannissement_delete', methods: ['POST'])]
    public function delete(Request $request, Bannissement $bannissement, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$bannissement->getId(), $request->get('_token'))) {
            $entityManager->remove($bannissement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_bannissement_index', [], Response::HTTP_SEE_OTHER);
    }
}
