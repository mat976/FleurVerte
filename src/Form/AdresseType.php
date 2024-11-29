<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de gestion des adresses
 * 
 * Ce formulaire permet la création et la modification d'adresses utilisateur,
 * incluant les informations de rue, code postal, ville et complément d'adresse.
 * Il offre également la possibilité de définir une adresse comme principale.
 */
class AdresseType extends AbstractType
{
    /**
     * Configure le formulaire avec les champs nécessaires
     * 
     * @param FormBuilderInterface $builder Le constructeur de formulaire
     * @param array $options Les options du formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rue', TextType::class, [
                'label' => 'Rue',
                'attr' => ['placeholder' => 'Ex: 123 rue de la Paix']
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['placeholder' => 'Ex: 75000']
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => ['placeholder' => 'Ex: Paris']
            ])
            ->add('complement', TextType::class, [
                'label' => 'Complément d\'adresse',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Appartement 4B, 2ème étage']
            ])
            ->add('principale', CheckboxType::class, [
                'label' => 'Adresse principale',
                'required' => false
            ]);
    }

    /**
     * Configure les options par défaut du formulaire
     * 
     * @param OptionsResolver $resolver Le résolveur d'options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }
}
