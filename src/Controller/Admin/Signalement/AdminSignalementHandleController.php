<?php
// src/Controller/Admin/AdminSignalementController.php

namespace App\Controller\Admin\Signalement;

use App\Entity\Signalement;
use App\Entity\Bannissement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/admin/signalement')]
class AdminSignalementHandleController extends AbstractController
{
    private $entityManager;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    #[Route('/{id}/handle', name: 'admin_signalement_handle', methods: ['POST'])]
    public function handle(int $id, Request $request): Response
    {
        $signalement = $this->entityManager->getRepository(Signalement::class)->find($id);
        $user = $signalement->getUtilisateur();

        if (!$signalement || !$user) {
            return new JsonResponse(['error' => 'Signalement ou utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Ban logic
        $banType = $request->request->get('ban_type');
        $bannissement = new Bannissement();
        $bannissement->setUtilisateur($user);
        $bannissement->setRaison('Violation des règles');
        $bannissement->setDateDeBannissement(new \DateTime());

        if ($banType === '7') {
            $bannissement->setDateFin(new \DateTime('+7 days'));
            $bannissement->setDefinitif(false);
            $bannissement->setDuree('7_jour');
            $this->entityManager->persist($bannissement);
            $signalement->setStatut(1);
            $bannissement->setStatut(1);

        } elseif ($banType === 'indefinite') {
            $bannissement->setDateFin(null);
            $bannissement->setDefinitif(true);
            $bannissement->setDuree('definitive');
            $this->entityManager->persist($bannissement);
            $signalement->setStatut(1);
            $bannissement->setStatut(1);

        }

        // Send email
        $email = (new Email())
            ->from('admin@yourapp.com')
            ->to($signalement->getUtilisateur()->getEmail())
            ->subject('Réponse à votre signalement')
            ->html($this->renderView('reponse/index.html.twig', [
                'signalement' => $signalement,
            ]));

        $this->mailer->send($email);

        $this->entityManager->flush();

        $this->addFlash('success', 'Réponse envoyée, utilisateur banni et signalement archivé.');

        return $this->redirectToRoute('admin_signalement_index');
    }
}
