<?php
// src/Controller/Admin/BannissementController.php

namespace App\Controller\Admin\Bannissement;

use App\Entity\Bannissement;
use App\Form\BannissementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/bannissement')]
class AdminBannissementNewController extends AbstractController
{
    #[Route('/new', name: 'admin_bannissement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
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
            'current_route' => 'admin', 
        ]);
    }
}
