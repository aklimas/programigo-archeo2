<?php

namespace App\Form\Core\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Stare hasło',
                'attr' => [
                    'class' => 'form-control-sm',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Hasło', 'attr' => ['class' => 'form-control-sm']],
                'second_options' => ['label' => 'Powtórz hasło', 'attr' => ['class' => 'form-control-sm']],
                'invalid_message' => 'Hasła muszą być identyczne.',
                'mapped' => false,

                'constraints' => [
                    new NotBlank([
                        'message' => 'Proszę wpisać hasło',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Twoje hasło powinno się składać z min {{ limit }} znaków',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save btn btn-primary btn-sm mt-4'],
                'label' => 'Zapisz',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => User::class,
        ]);
    }
}
