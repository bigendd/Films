<?php
namespace App\Controller\Avis;

use App\Entity\Avis;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TmdbApiService;  // Ajoute ceci

class AvisEditController extends AbstractController
{
    private $entityManager;
    private $tmdbApiService;  // Ajoute ceci

    public function __construct(EntityManagerInterface $entityManager)  
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/avis/{id}/edit', name: 'avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avis): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser() !== $avis->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('film_detail', ['id' => $avis->getFilmId()]);
        }

        return $this->render('avis/edit.html.twig', [
            'avis' => $avis,
            'form' => $form->createView(),
        ]);
    }
}