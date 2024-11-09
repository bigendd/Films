<?php
// src/Form/ProfileType.php

namespace App\Form;

use App\Entity\InfoUtilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('dateDeNaissance', DateType::class, [
                'label' => 'Date de Naissance',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('adressePostale', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
            ])
            ->add('numeroDeTelephone', TextType::class, [
                'label' => 'Numéro de Téléphone',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoUtilisateur::class,
        ]);
    }
}
