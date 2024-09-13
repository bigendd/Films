<?php
namespace App\Controller\Avis;

use App\Entity\Avis;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TmdbApiService;  

class AvisNewController extends AbstractController
{
    private $entityManager;
    private $tmdbApiService; 

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService)  
    {
        $this->entityManager = $entityManager;
        $this->tmdbApiService = $tmdbApiService;  
    }

    #[Route('/avis/new/{filmId}', name: 'avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $filmId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Empêcher les administrateurs de soumettre des avis
        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Les administrateurs ne peuvent pas laisser des avis.');
            return $this->redirectToRoute('film_detail', ['id' => $filmId]);
        }

        $filmDetails = $this->tmdbApiService->getMovieDetails($filmId);
        if (!$filmDetails) {
            throw $this->createNotFoundException('Le film n\'existe pas.');
        }

        $avis = new Avis();
        $avis->setTitre($filmDetails['title']);  

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setUtilisateur($this->getUser());
            $avis->setDateDeCreation(new \DateTime());
            $avis->setFilmId($filmId);

            $this->entityManager->persist($avis);
            $this->entityManager->flush();

            return $this->redirectToRoute('film_detail', ['id' => $filmId]);
        }

        return $this->render('avis/new.html.twig', [
            'avis' => $avis,
            'form' => $form->createView(),
            'current_route' => 'formulaire', 

        ]);
    }
}