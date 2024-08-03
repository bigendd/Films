<?php
// src/Controller/Admin/BannissementController.php

namespace App\Controller\Admin\Bannissement;

use App\Entity\Bannissement;
use App\Form\BannissementType;
use App\Repository\BannissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/bannissement')]
class AdminBannissementEditController extends AbstractController
{
    #[Route('/{id}/edit', name: 'admin_bannissement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bannissement $bannissement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BannissementType::class, $bannissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_bannissement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/bannissement/edit.html.twig', [
            'bannissement' => $bannissement,
            'form' => $form,
            'current_route' => 'admin', 
        ]);
    }

 
}
