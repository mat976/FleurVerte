<?php

namespace App\Form;

use App\Entity\Fleur;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Formulaire de gestion des produits (fleurs)
 * 
 * Ce formulaire permet la création et la modification des produits,
 * incluant toutes les caractéristiques importantes d'une fleur :
 * nom, description, prix, taux THC, stock et statut épinglé.
 */
class FleurType extends AbstractType
{
    /**
     * Configure le formulaire avec tous les champs du produit
     * 
     * Inclut :
     * - Nom du produit
     * - Description détaillée
     * - Prix en euros
     * - Taux de THC (avec précision décimale)
     * - Gestion du stock
     * - Option pour épingler le produit
     * 
     * @param FormBuilderInterface $builder Le constructeur de formulaire
     * @param array $options Les options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
            ])
            ->add('Thc', NumberType::class, [
                'label' => 'Taux THC',
                'scale' => 2,
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock disponible',
                'attr' => [
                    'min' => 0,
                    'placeholder' => 'Entrez la quantité en stock'
                ]
            ])
            ->add('isPinned', CheckboxType::class, [
                'label' => 'Épingler ce produit',
                'required' => false,
                'attr' => [
                    'class' => 'h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded'
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image du produit',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_uri' => false,
                'image_uri' => true,
                'asset_helper' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Tags',
                'attr' => [
                    'class' => 'tag-selector'
                ],
                'by_reference' => false,
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
            'data_class' => Fleur::class,
        ]);
    }
}
