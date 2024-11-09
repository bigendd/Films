<?php

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
        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // On crée un nouveau bannissement
        $bannissement = new Bannissement();
        // On crée le formulaire pour ce bannissement
        $form = $this->createForm(BannissementType::class, $bannissement);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $duree = $form->get('duree')->getData();

            // On vérifie la durée sélectionnée pour configurer le bannissement
            if ($duree === '7_jours') {
                // Bannissement temporaire de 7 jours
                $bannissement->setDateFin((new \DateTime())->modify('+7 days'));
                $bannissement->setDefinitif(false);
                $bannissement->setStatut(true);
            } elseif ($duree === 'definitif') {
                // Bannissement définitif
                $bannissement->setDefinitif(true);
                $bannissement->setDateFin(null);
                $bannissement->setStatut(true);
            }

            // On sauvegarde le nouveau bannissement dans la base de données
            $entityManager->persist($bannissement);
            $entityManager->flush();

            // On redirige vers la liste des bannissements
            return $this->redirectToRoute('admin_bannissement_index');
        }

        // On affiche le formulaire pour créer un nouveau bannissement
        return $this->render('admin/bannissement/new.html.twig', [
            'bannissement' => $bannissement,  // Le nouveau bannissement à afficher dans le formulaire
            'form' => $form->createView(),  // La vue du formulaire
            'current_route' => 'admin',  // La route actuelle pour la vue
        ]);
    }
}
