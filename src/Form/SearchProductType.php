<?php

namespace App\Form;

use App\Entity\Fleur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

/**
 * Formulaire de recherche pour les produits
 */
class SearchProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', SearchType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher une variété...',
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('sort', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'choices' => [
                    'Nom (A-Z)' => 'name_asc',
                    'Nom (Z-A)' => 'name_desc',
                    'Prix croissant' => 'price_asc',
                    'Prix décroissant' => 'price_desc',
                ],
                'attr' => [
                    'class' => 'px-4 py-2 border border-gray-300 border-l-0 rounded-r-md bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}