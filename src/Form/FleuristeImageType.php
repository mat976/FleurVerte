<?php

namespace App\Form;

use App\Entity\FleuristeImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Formulaire pour gérer les images d'un fleuriste
 * 
 * Ce formulaire permet d'uploader une image et d'ajouter une légende.
 * Il est utilisé dans une collection du formulaire FleuristeType.
 */
class FleuristeImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_uri' => false,
                'image_uri' => true,
                'asset_helper' => true,
                'label' => 'Image'
            ])
            ->add('caption', TextType::class, [
                'required' => false,
                'label' => 'Légende',
                'attr' => [
                    'placeholder' => 'Décrivez cette image'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FleuristeImage::class,
        ]);
    }
}
