<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType // Déclaration de la classe ResetPasswordRequestFormType qui étend AbstractType
{
    // Méthode pour construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [ // Ajout du champ email
                'attr' => ['autocomplete' => 'email'], // Attribut pour l'autocomplétion
                'constraints' => [
                    new NotBlank([ // Contrainte pour s'assurer que le champ n'est pas vide
                        'message' => 'Veuillez entrer votre adresse e-mail', // Message d'erreur
                    ]),
                ],
            ])
        ;
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]); // Configuration des options par défaut (aucune option particulière)
    }
}
