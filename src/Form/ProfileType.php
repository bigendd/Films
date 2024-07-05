<?php
// src/Form/ProfileType.php

namespace App\Form;

use App\Entity\InfoUtilisateur;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Champs pour Utilisateur
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            // Champs pour InfoUtilisateur
            ->add('infoUtilisateur', EntityType::class, [
                'class' => InfoUtilisateur::class,
                'choice_label' => 'nom', // par exemple
                'label' => 'Nom',
                'mapped' => false, // on va le gérer manuellement dans le contrôleur
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('iu')
                        ->where('iu.utilisateur = :utilisateur')
                        ->setParameter('utilisateur', $options['data']);
                },
            ])
            ->add('nom', TextType::class, [
                'mapped' => false,
                'label' => 'Nom',
            ])
            ->add('photoDeProfil', TextType::class, [
                'mapped' => false,
                'label' => 'Photo de Profil',
            ])
            ->add('dateDeNaissance', TextType::class, [
                'mapped' => false,
                'label' => 'Date de Naissance',
            ])
            ->add('genre', TextType::class, [
                'mapped' => false,
                'label' => 'Genre',
            ])
            ->add('adresse', TextType::class, [
                'mapped' => false,
                'label' => 'Adresse',
            ])
            ->add('numeroDeTelephone', TextType::class, [
                'mapped' => false,
                'label' => 'Numéro de Téléphone',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
