<?php

namespace App\Controller\Contact;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->getUser();

            if (method_exists($user, 'getEmail')) {
                $contact->setUtilisateur($user);
                $contact->setEmail($user->getEmail());
            } else {
                throw new \RuntimeException('La méthode getEmail() n\'est pas définie pour l\'utilisateur.');
            }
        }

        $form = $this->createForm(ContactType::class, $contact, [
            'is_authenticated' => $this->isGranted('IS_AUTHENTICATED_FULLY'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setDateDenvoie(new \DateTime());
            $contact->setStatut(false);

            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_new');
        }

        return $this->render('contact/new.html.twig', [
            'form' => $form->createView(),
            'current_route' => 'formulaire',

        ]);
    }
}
