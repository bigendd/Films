<?php

namespace App\Controller\Avis;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvisArchiveController extends AbstractController 
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)  
    {
        $this->entityManager = $entityManager; // Initialise l'EntityManager
    }

    #[Route('/avis/{id}/delete', name: 'avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avis): Response 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // Vérifie que l'utilisateur est authentifié
        
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        // Vérifie si l'utilisateur est le propriétaire de l'avis ou un administrateur
        if ($user !== $avis->getUtilisateur() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException(); // Lève une exception si l'accès est refusé
        }
    
        // Modifie le statut de l'avis à archivé (statut 1)
        $avis->setStatut(1); // Supposons que le statut 1 signifie "archivé"
        
        // Enregistre qui a archivé l'avis et la date d'archivage
        if ($this->isGranted('ROLE_ADMIN')) {
            $avis->setArchiverPar('admin'); // Si l'utilisateur est admin, il est noté comme tel
        } else {
            $avis->setArchiverPar($user->getEmail()); // Sinon, on utilise l'email de l'utilisateur
        }
    
        $avis->setDateArchivage(new \DateTime()); // Définit la date d'archivage
    
        $this->entityManager->flush(); // Enregistre les modifications dans la base de données
    
        return $this->redirectToRoute('film_detail', ['id' => $avis->getFilmId()]); // Redirige vers les détails du film
    }
}
