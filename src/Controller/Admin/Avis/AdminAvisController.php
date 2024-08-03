<?php
// src/Controller/Admin/AdminAvisController.php

 namespace App\Controller\Admin\Avis;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminAvisController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }

    #[Route('/admin/avis', name: 'admin_avis_index', methods: ['GET'])]
    public function index(): Response
    {
        $avis = $this->entityManager->getRepository(Avis::class)->findBy(['statut' => 0]);
        
        return $this->render('admin/avis/index.html.twig', [
            'avis' => $avis,
            'current_route' => 'admin', 
        ]);
    }

       // #[Route('/{id}/archive', name: 'admin_avis_archive', methods: ['POST'])]
      //  public function archive(int $id): Response
    //{
    //$avis = $this->entityManager->getRepository(Avis::class)->find($id);

    //if (!$avis) {
      //  return new JsonResponse(['error' => 'Avis non trouvé'], Response::HTTP_NOT_FOUND);
    //}

    // Set status to archived
    //$avis->setStatut(1); // Assuming you have a status field to mark as archived

    // Set archivedBy (admin) and dateArchivage
    //$avis->setArchiverPar('admin');
    //$avis->setDateArchivage(new \DateTime());

    //$this->entityManager->flush();

    //$this->addFlash('success', 'Avis archivé.');

    //return $this->redirectToRoute('admin_avis_index');
   // }

}
