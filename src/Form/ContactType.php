<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', null, [
                'row_attr' => [
                    'class' => 'col-6 me-auto px-1 text-center'
                ]
            ])
            ->add('nom', null, [
                'row_attr' => [
                    'class' => 'col-6 ms-auto px-1 text-center'
                ]
            ])
            ->add('telephone', null, [
                'row_attr' => [
                    'class' => 'col-6 ms-auto px-1 text-center'
                ]
            ])
            ->add('email', EmailType::class, [
                'row_attr' => [
                    'class' => 'col-6 ms-auto px-1 text-center'
                ]
            ])
            ->add('message', TextareaType::class, [
                'row_attr' => [
                    'class' => 'px-1 text-center'
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
