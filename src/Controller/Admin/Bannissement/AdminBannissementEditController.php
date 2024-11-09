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
class AdminBannissementEditController extends AbstractController
{
    #[Route('/{id}/edit', name: 'admin_bannissement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bannissement $bannissement, EntityManagerInterface $entityManager): Response
    {
        // Vérification que l'utilisateur a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // On crée le formulaire pour éditer le bannissement
        $form = $this->createForm(BannissementType::class, $bannissement);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On sauvegarde les changements dans la base de données
            $entityManager->flush();

            // On redirige vers la liste des bannissements
            return $this->redirectToRoute('admin_bannissement_index', [], Response::HTTP_SEE_OTHER);
        }

        // On affiche la vue avec le formulaire pour éditer le bannissement
        return $this->render('admin/bannissement/edit.html.twig', [
            'bannissement' => $bannissement,  // Le bannissement à éditer
            'form' => $form->createView(),  // Le formulaire affiché dans la vue
            'current_route' => 'admin',  // La route actuelle pour la vue
        ]);
    }
}
