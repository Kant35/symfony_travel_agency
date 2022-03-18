<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label'=>'Mon Mot de passe Actuel',
                'attr'=>[
                    'placeholder'=>'Veuillez saisir votre Mot de passe'
                ],
                'row_attr' => [
                    'class' => 'd-flex flex-column col-md-12 mb-3 px-2'
                ]
            ])
            // Définition du nouveau mot de passe avec double vérification
            ->add('new_password', RepeatedType::class, [
                'type'=> PasswordType::class,
                // On a la possibilité ici de mettre des contraintes sur notre formulaire sans passer par l'entité. 
                // 'constraints'=> new Length([
                //     'min'=>3,
                //     'max'=>15
                // ]),
                'invalid_message'=>'Le Mot de Passe et la confirmation doivent être identique',
                'required'=>true,
                'first_options'=>[
                    'label'=>'Nouveau Mot de Passe',
                    'attr'=>[
                        'placeholder'=>'Mon nouveau mot de Passe',
                    ],
                    'row_attr' => [
                        'class' => 'd-flex flex-column col-md-12 mb-3 px-2'
                    ]
                ], 
                'second_options'=>[
                    'label'=>'Confirmez votre Nouveau Mot de Passe',
                    'attr'=>[
                        'placeholder'=>'Confirmez votre nouveau mot de Passe',
                    ],
                    'row_attr' => [
                        'class' => 'd-flex flex-column col-md-12 mb-3 px-2'
                    ]
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
