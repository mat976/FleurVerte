<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * Formulaire de gestion du profil utilisateur
 */
class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
            ])
            ->add('becomeFleuriste', ChoiceType::class, [
                'label' => 'Type de compte',
                'choices' => [
                    'Client' => '0',
                    'Fleuriste (vendeur)' => '1',
                ],
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
            ])
            ->add('shopName', TextType::class, [
                'label' => 'Nom de votre boutique',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Ma Belle Boutique Florale',
                ],
            ])

            ->add('avatarName', TextType::class, [
                'label' => 'Avatar sélectionné',
                'mapped' => false,
                'required' => false,
            ])
            ->add('avatarFile', FileType::class, [
                'label' => 'Télécharger un nouvel avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG)',
                    ])
                ],
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
            'data_class' => User::class,
        ]);
    }
}
