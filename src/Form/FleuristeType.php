<?php

namespace App\Form;

use App\Entity\Fleuriste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de gestion des informations fleuriste
 * 
 * Ce formulaire permet la création et la modification des informations
 * d'un fleuriste, incluant les coordonnées et la description du magasin.
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du magasin',
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ]);
    }

    /**
     * Configure les options par défaut du formulaire
     * 
     * @param OptionsResolver $resolver Le résolveur d'options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fleuriste::class,
        ]);
    }
}
