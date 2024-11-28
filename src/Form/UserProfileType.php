<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserProfileType extends AbstractType
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
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Client' => 'ROLE_USER',
                    'Fleuriste' => 'ROLE_FLEURISTE',
                ],
                'multiple' => false,
                'expanded' => false,
                'data' => 'ROLE_USER',
                'mapped' => false
            ])

            ->add('avatarName', ChoiceType::class, [
                'label' => 'Choisissez votre avatar',
                'choices' => [
                    'Avatar 1' => '1.png',
                    'Avatar 2' => '2.png',
                    'Avatar 3' => '3.png',
                    'Avatar 4' => '4.png',
                    'Avatar 5' => '5.png',
                    'Avatar 6' => '6.png',
                    'Avatar 7' => '7.png',
                    'Avatar 8' => '8.png',
                    'Avatar 9' => '9.png',
                    'Avatar 10' => '10.png',
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => false,
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'sr-only avatar-radio'];
                },
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
