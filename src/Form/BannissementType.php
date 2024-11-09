<?php

namespace App\Form;

use App\Entity\Bannissement;
use App\Entity\Utilisateur;
use App\Repository\BannissementRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BannissementType extends AbstractType
{
    private $tokenStorage;
    private $bannissementRepository;
    public function __construct(TokenStorageInterface $tokenStorage, BannissementRepository $bannissementRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->bannissementRepository = $bannissementRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer l'utilisateur connecté
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $builder
            ->add('raison', TextType::class, [
                'attr' => ['class' => 'form-group-raison'],
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'email',
                'query_builder' => fn() => $this->bannissementRepository->findUtilisateursSansBannissementActif($currentUser),
                'attr' => ['class' => 'form-group-utilisateur'],
            ])
            ->add('duree', ChoiceType::class, [
                'choices' => [
                    '7 jours' => '7_jours',
                    'Définitif' => 'definitif',
                ],
                'label' => 'Durée du bannissement',
                'attr' => ['class' => 'form-group-duree'],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bannissement::class,
        ]);
    }
}
