<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('reference')
            // ->add('date_reservation')
            ->add('date_depart', DateType::class, [
                'label' => 'Sélectionnez votre date de départ',
                'html5' => true,
                'widget' => 'single_text',
                'row_attr' => [
                    'class' => 'text-center'
                ],
                'attr' => [
                    'class' => 'js-datepicker',
                    'min' => date('Y-m-d', strtotime('+2 weeks')) // Limiter la sélection de la date à une date dans 2 semaines minimum
                ]
            ])
            ->add('participants', CollectionType::class, [
                'entry_type' => ParticipantType::class,
                'allow_delete' => true,
                'allow_add' => true
            ])
            // ->add('prix_total')
            // ->add('statut')
            // ->add('produit')
            // ->add('client')
        ;
        if ($options['admin'] == true) {
            $builder
                ->add('statut', ChoiceType::class, [
                    'choices' => [
                        'En Attente' => 'En Attente',
                        'Validé' => 'Validé',
                        'Terminé' => 'Terminé'
                    ]
                ])
            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'admin' => false
        ]);
    }
}
