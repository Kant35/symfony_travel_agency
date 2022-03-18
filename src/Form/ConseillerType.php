<?php

namespace App\Form;

use App\Entity\Conseiller;
use App\Entity\Destination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ConseillerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
        ;
            //  Dans le controller nous avons passé en paramètre lors de l'edit la valeur 'true' à l'option 'edit'. Du coup lorsque nous sommes dans le formulaire de modification nous n'avons pas accès au mot de passe.
            if ($options['edit'] == false) {
                $builder
                    ->add('password', PasswordType::class, [
                        'label' => 'Mot de Passe'
                    ])
                ;
            }

        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Conseiller' => 'ROLE_CONSEILLER',
                    'Administrateur' => 'ROLE_ADMIN'
                ], 
                'multiple' => true, 
                'expanded' => true
            ])
            ->add('prenom')
            ->add('nom')
            ->add('referent', ChoiceType::class, [
                'label' => 'Référent Destination',
                'choices' => [
                    'non référent' => false,
                    'référent' => true,
                ], 
                'expanded' => true
            ])
            ->add('photoFile', VichImageType::class, [
                'required' => $options['required_photo'], // En fonction de la valeur de $options['required_photo'] le caractère required de la photo sera true ou false
                'label' => 'Photo',
                'allow_delete' => false,
                'download_uri' => false
            ])
            
            ->add('description')
            ->add('specialite', EntityType::class, [
                'class' => Destination::class, 
                'choice_label' => 'titre'
            ])
        ;


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conseiller::class,
            // On définit ici des options par défaut pour pouvoir manipuler et modifier notre formulaire
            'edit' => false,
            'required_photo' => true
        ]);
    }
}
