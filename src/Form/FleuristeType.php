<?php

namespace App\Form;

use App\Entity\Fleuriste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire pour gérer les informations d'un fleuriste
 * 
 * Ce formulaire permet de gérer :
 * - Les informations de base (nom, description)
 * - Les coordonnées (adresse, téléphone, email)
 * - Les images du magasin (collection d'images)
 * 
 * Tous les champs sont optionnels pour permettre une édition progressive du profil.
 */
class FleuristeType extends AbstractType
{
    /**
     * Configure le formulaire avec les champs du fleuriste
     * 
     * Inclut les informations essentielles du magasin :
     * - Nom du magasin
     * - Adresse physique
     * - Numéro de téléphone
     * - Adresse email
     * - Description du magasin (optionnelle)
     * 
     * @param FormBuilderInterface $builder Le constructeur de formulaire
     * @param array $options Les options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'label' => 'Nom du magasin',
                'attr' => [
                    'placeholder' => 'Entrez le nom de votre magasin',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez votre magasin',
                    'class' => 'form-control',
                    'rows' => 5
                ]
            ])
            ->add('adresse', TextType::class, [
                'required' => false,
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Entrez l\'adresse de votre magasin',
                    'class' => 'form-control'
                ]
            ])
            ->add('telephone', TelType::class, [
                'required' => false,
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Entrez votre numéro de téléphone',
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Entrez votre adresse email',
                    'class' => 'form-control'
                ]
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => FleuristeImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'required' => false
            ])
        ;
    }

    /**
     * Configure les options par défaut du formulaire
     * 
     * @param OptionsResolver $resolver Le résolveur d'options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fleuriste::class,
        ]);
    }
}
