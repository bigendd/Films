<?php
namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TmdbApiService;  // Ajoute ceci

class AvisController extends AbstractController
{
    private $entityManager;
    private $tmdbApiService;  // Ajoute ceci

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService)  // Modifie ceci
    {
        $this->entityManager = $entityManager;
        $this->tmdbApiService = $tmdbApiService;  // Modifie ceci
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
        $avis->setTitre($filmDetails['title']);  // Set the film title

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
        ]);
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

    #[Route('/avis/{id}/delete', name: 'avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avis): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->getUser() !== $avis->getUtilisateur()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$avis->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($avis);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('film_detail', ['id' => $avis->getFilmId()]);
    }
}
