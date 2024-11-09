<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType // Déclaration de la classe RegistrationFormType
{
    // Méthode pour construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [ // Ajout d'un champ pour l'email
                'label' => 'Email', // Label du champ
                'constraints' => [ // Contraintes de validation
                    new NotBlank([ // Vérifie que le champ n'est pas vide
                        'message' => 'Veuillez entrer votre email',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [ // Ajout d'un champ pour le mot de passe (répété)
                'type' => PasswordType::class, // Type de champ pour le mot de passe
                'invalid_message' => 'Les champs de mot de passe doivent correspondre.', // Message d'erreur si les mots de passe ne correspondent pas
                'options' => ['attr' => ['class' => 'password-field']], // Options pour le champ (ex. classe CSS)
                'required' => true, // Champ requis
                'first_options' => ['label' => 'Mot de passe'], // Options pour le premier champ de mot de passe
                'second_options' => ['label' => 'Répéter le mot de passe'], // Options pour le second champ de mot de passe
            ])
            ->add('agreeTerms', CheckboxType::class, [ // Ajout d'une case à cocher pour accepter les termes
                'label' => 'J\'accepte les termes', // Label du champ
                'constraints' => [ // Contraintes de validation
                    new IsTrue([ // Vérifie que la case est cochée
                        'message' => 'Vous devez accepter nos termes'
                    ])
                ]
            ]);
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, // Pas de classe de données associée
        ]);
    }
}
