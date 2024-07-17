<?php
// src/Form/BannissementType.php

namespace App\Form;

use App\Entity\Bannissement;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BannissementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raison')
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'email',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.bannissement', 'b')
                        ->where('b.id IS NULL OR b.dateFin < :now')
                        ->setParameter('now', new \DateTime());
                },
            ])
            ->add('duree', ChoiceType::class, [
                'choices' => [
                    '7 jours' => '7_jours',
                    'Définitif' => 'definitif',
                ],
                'label' => 'Durée du bannissement',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bannissement::class,
        ]);
    }
}
