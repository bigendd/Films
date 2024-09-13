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
        $this->entityManager = $entityManager;
    }

    #[Route('/avis/{id}/delete', name: 'avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avis): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        if ($user !== $avis->getUtilisateur() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
    
        // Modifier le statut de l'avis à archivé (statut 1)
        $avis->setStatut(1); // Supposons que vous avez une méthode setStatus() dans votre entité Avis
        
        // Enregistrer qui a archivé l'avis et la date d'archivage
        if ($this->isGranted('ROLE_ADMIN')) {
            $avis->setArchiverPar('admin');
        } else {
            $avis->setArchiverPar($user->getEmail());
        }
    
        $avis->setDateArchivage(new \DateTime());
    
        $this->entityManager->flush();
    
        return $this->redirectToRoute('film_detail', ['id' => $avis->getFilmId()]);
    }
}
