<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Signalement;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignalementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raison')
            ->add('dateDeCreation', null, [
                'widget' => 'single_text',
            ])
            ->add('statut')
            ->add('avis', EntityType::class, [
                'class' => Avis::class,
                'choice_label' => 'id',
            ])
            ->add('utilisateurQuiSignale', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Signalement::class,
        ]);
    }
}
