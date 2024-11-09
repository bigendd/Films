<?php
namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class ContactType extends AbstractType // Déclaration de la classe ContactType qui étend AbstractType
{
    // Méthode pour construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Si l'utilisateur n'est pas authentifié, ajouter le champ email
        if (!$options['is_authenticated']) {
            $builder->add('email', EmailType::class, [
                'label' => 'Email', // Label pour le champ email
            ]);
        }

        $builder
            ->add('objet', TextType::class, [ // Ajout du champ objet
                'label' => 'Objet', // Label pour le champ objet
            ])
            ->add('corps', TextareaType::class, [ // Ajout du champ corps pour le message
                'label' => 'Message', // Label pour le champ message
                'attr' => ['class' => 'form-control', 'rows' => 5], // Attributs pour le champ
            ]);
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class, // Lien avec l'entité Contact
            'is_authenticated' => false, // Valeur par défaut pour l'option is_authenticated
        ]);
    }
}
