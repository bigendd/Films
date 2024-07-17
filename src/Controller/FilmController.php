<?php
namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Favoris;
use App\Repository\AvisRepository;
use App\Service\TmdbApiService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    private $entityManager;
    private $tmdbApiService;
    private $avisRepository;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, TmdbApiService $tmdbApiService, AvisRepository $avisRepository, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->tmdbApiService = $tmdbApiService;
        $this->avisRepository = $avisRepository;
        $this->mailer = $mailer;
    }

    // ...

    #[Route('/avis/{id}/archive', name: 'avis_archive', methods: ['POST'])]
    public function archive(int $id, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $avi = $this->entityManager->getRepository(Avis::class)->find($id);
        if ($avi) {
            $avi->setStatut(true);

            // Retrieve the title of the film from the Avis entity
            $filmTitle = $avi->getTitre() ?? 'le film';

            // Send email notification
            $email = (new TemplatedEmail())
                ->from('admin@yourapp.com')
                ->to($avi->getUtilisateur()->getEmail())
                ->subject('Votre avis a été archivé')
                ->htmlTemplate('reponse_archive/reponse_archive.html.twig')
                ->context([
                    'avi' => $avi,
                    'filmTitle' => $filmTitle,
                ]);

            $this->mailer->send($email);

            $this->entityManager->flush();

            $this->addFlash('notice', 'L\'avis a été archivé et l\'utilisateur a été notifié.');
        }

        return $this->redirectToRoute('film_detail', ['id' => $avi->getFilmId()]);
    }

    #[Route('/', name: 'film_list')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $searchQuery = $request->query->get('search', '');
        $page = $request->query->getInt('page', 1);
    
        if ($searchQuery) {
            $films = $this->tmdbApiService->getMovies('/search/movie', [
                'query' => $searchQuery,
                'page' => $page
            ]);
        } else {
            $films = $this->tmdbApiService->getMovies('/discover/movie', [
                'page' => $page
            ]);
        }
    
        // Debugging API response
        if (!isset($films['results']) || empty($films['results'])) {
            return $this->render('film/index.html.twig', [
                'films' => [],
                'searchQuery' => $searchQuery,
                'totalPages' => 0,
                'currentPage' => $page,
                'debug' => [
                    'totalItems' => 0,
                    'itemsPerPage' => 0,
                    'totalPages' => 0,
                    'apiResponse' => json_encode($films)
                ]
            ]);
        }

        // Pagination configuration
        $pagination = $paginator->paginate(
            $films['results'],
            $page,
            20  // Nombre d'éléments par page (20 films par page)
        );
    
        // Calcul du nombre total d'éléments et de pages
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = 20;  // Nombre d'éléments par page (20 films par page)
        $totalPages = min(10, ceil($totalItems / $itemsPerPage));  // Limite à 10 pages maximum
    
        return $this->render('film/index.html.twig', [
            'films' => $pagination,
            'searchQuery' => $searchQuery,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'debug' => [
                'totalItems' => $totalItems,
                'itemsPerPage' => $itemsPerPage,
                'totalPages' => $totalPages,
                'apiResponse' => json_encode($films)
            ]
        ]);
    }

    #[Route('/favorites', name: 'favorite_list')]
    public function favoriteList(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $favorites = $this->entityManager->getRepository(Favoris::class)->findBy(['utilisateur' => $user]);

        return $this->render('film/favorie.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/film/{id}', name: 'film_detail')]
    public function detail(int $id): Response
    {
        $filmDetails = $this->tmdbApiService->getMovieDetails($id);

        if (!$filmDetails) {
            throw new NotFoundHttpException('Film not found in TMDb');
        }

        $user = $this->getUser();
        $isFavorite = false;

        if ($user) {
            $favorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]);
            $isFavorite = $favorite !== null;
        }

        // Fetch only reviews with statut false
        $avis = $this->avisRepository->findByFilmIdAndStatus($id, false);

        return $this->render('film/detail.html.twig', [
            'film' => $filmDetails,
            'isFavorite' => $isFavorite,
            'avis' => $avis,
        ]);
    }

    #[Route('/film/{id}/favorite', name: 'film_add_favorite', methods: ['POST'])]
    public function addFavorite(int $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $existingFavorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'filmId' => $id]);

        if ($existingFavorite) {
            return $this->redirectToRoute('favorite_list');
        }

        $filmDetails = $this->tmdbApiService->getMovieDetails($id);

        if (!$filmDetails) {
            throw new NotFoundHttpException('Film not found in TMDb');
        }

        $favorie = new Favoris();
        $favorie->setTitre($filmDetails['title']);
        $favorie->setFilmId($id);
        $favorie->setUtilisateur($user);
        $favorie->setDateDeCreation(new \DateTime());
        $favorie->setStatut(false);  // Ensure statut is set to false

        $this->entityManager->persist($favorie);
        $this->entityManager->flush();

        return $this->redirectToRoute('film_detail', ['id' => $id]);
    }

    #[Route('/{id}/delete', name: 'favorite_delete', methods: ['POST'])]
    public function deleteFavorite(Request $request, int $id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'User not authenticated.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier le jeton CSRF
        if (!$this->isCsrfTokenValid('delete_favorite_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('favorite_list');
        }

        $favorite = $this->entityManager->getRepository(Favoris::class)->findOneBy(['utilisateur' => $user, 'id' => $id]);

        if ($favorite) {
            $this->entityManager->remove($favorite);
            $this->entityManager->flush();
        } else {
            $this->addFlash('error', 'Favorite not found.');
        }

        return $this->redirectToRoute('favorite_list');
    }

    #[Route('/favorites/clear', name: 'favorite_clear', methods: ['POST'])]
    public function clearFavorites(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $favorites = $this->entityManager->getRepository(Favoris::class)->findBy(['utilisateur' => $user]);

        foreach ($favorites as $favorite) {
            $this->entityManager->remove($favorite);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('favorite_list');
    }
}
