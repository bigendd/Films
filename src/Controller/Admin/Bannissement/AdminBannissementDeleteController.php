<?php
// src/Controller/Admin/BannissementController.php

namespace App\Controller\Admin\Bannissement;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Bannissement;
use App\Repository\BannissementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/bannissement')]
class AdminBannissementDeleteController extends AbstractController
{
    #[Route('/{id}/delete', name: 'admin_bannissement_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Bannissement $bannissement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bannissement->getId(), $request->get('_token'))) {
            $entityManager->remove($bannissement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_bannissement_index', [], Response::HTTP_SEE_OTHER);
    }
}
