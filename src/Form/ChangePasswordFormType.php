<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class ChangePasswordFormType extends AbstractType // Déclaration de la classe ChangePasswordFormType qui étend AbstractType
{
    // Méthode pour construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [ // Ajout d'un champ pour le mot de passe
                'type' => PasswordType::class, // Type du champ est Password
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password', // Attribut pour le champ
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([ // Contrainte pour vérifier que le champ n'est pas vide
                            'message' => 'Rentrer un mot de passe ',
                        ]),
                        new Length([ // Contrainte pour la longueur du mot de passe
                            'min' => 4,
                            'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                            'max' => 4096,
                        ]),
                        new PasswordStrength(), // Contrainte pour vérifier la force du mot de passe
                        new NotCompromisedPassword(), // Contrainte pour vérifier si le mot de passe a été compromis
                    ],
                    'label' => 'Nouveau mot de passe', // Label pour le champ
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe', // Label pour le champ répété
                ],
                'invalid_message' => 'Le mot de passe n\'est pas pareil.', // Message d'erreur si les mots de passe ne correspondent pas
                'mapped' => false, // Ne pas lier ce champ à une entité
            ])
        ;
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]); // Options par défaut (aucune option par défaut spécifiée)
    }
}
