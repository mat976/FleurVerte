<?php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de gestion des tags
 * 
 * Ce formulaire permet la création et la modification des tags,
 * incluant leur nom et leur couleur pour l'affichage.
 */
class TagType extends AbstractType
{
    /**
     * Configure le formulaire avec tous les champs du tag
     * 
     * @param FormBuilderInterface $builder Le constructeur de formulaire
     * @param array $options Les options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du tag',
                'attr' => [
                    'placeholder' => 'Entrez le nom du tag',
                    'class' => 'shadow-sm focus:ring-green-500 focus:border-green-500 block w-full sm:text-sm border-gray-300 rounded-md'
                ]
            ])
            ->add('couleur', ColorType::class, [
                'label' => 'Couleur',
                'required' => false,
                'attr' => [
                    'class' => 'h-10 w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500'
                ]
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
            'data_class' => Tag::class,
        ]);
    }
}
