<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class AvisType extends AbstractType // Déclaration de la classe AvisType qui étend AbstractType
{
    // Méthode pour construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire', TextareaType::class, [ // Ajout d'un champ pour le commentaire
                'attr' => ['class' => 'form-control', 'rows' => 5], // Attributs HTML pour le champ (classe CSS et nombre de lignes)
            ]);
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class, // Associe le formulaire à la classe Avis
        ]);
    }
}
