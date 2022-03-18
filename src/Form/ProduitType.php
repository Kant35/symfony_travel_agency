<?php

namespace App\Form;

use App\Entity\Produit;
use App\Form\EtapeType;
use App\Entity\Destination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('photoFile', VichImageType::class, [
                'required' => false,
                'label' => 'Illustration Produit', 
                'allow_delete' => false,
                'download_uri' => false
            ])
            ->add('prix', IntegerType::class)
            ->add('destinations', EntityType::class, [
                'class' => Destination::class, 
                'choice_label' => 'titre', 
                'multiple' => true,
                'expanded' => true
            ])
            ->add('etapes', CollectionType::class, [
                'entry_type'=> EtapeType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
