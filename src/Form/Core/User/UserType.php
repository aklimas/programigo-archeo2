<?php

namespace App\Form\Core\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserType extends AbstractType
{
    private ?AuthorizationCheckerInterface $authorization = null;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorization = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control form-control-sm mb-3'],
            ]);

        $roles = [];

        if ($this->authorization->isGranted('ROLE_ADMIN')) {
            $roles = array_merge($roles, ['Podgląd użytkownika' => ['Tak' => 'ROLE_ALLOWED_TO_SWITCH']]);
        }

        if ($this->authorization->isGranted('ROLE_SUPER_ADMIN')) {
            $roles = array_merge($roles, ['Administrator' => ['Tak' => 'ROLE_ADMIN']]);
            $roles = array_merge($roles, ['Super Administrator' => ['Tak' => 'ROLE_SUPER_ADMIN']]);
        }

        if ($this->authorization->isGranted('ROLE_SUPER_ADMIN')) {
            $builder->add('roles', ChoiceType::class, [
                    'label' => 'Role użytkownika',
                    'attr' => ['class' => 'form-control mb-3',
                        'style' => 'height:400px',
                    ],
                    'choices' => $roles,
                    'multiple' => true,
                    'required' => true,
                ]
            )
                ->add('isVerified', ChoiceType::class, [
                    'label' => 'Czy konto jest aktywne?',
                    'label_attr' => ['class' => 'control-label', 'style' => 'margin-top:7px;'],
                    'choices' => [
                        'Tak' => 1,
                        'Nie' => 0,
                    ],
                    'expanded' => true,
                    'multiple' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }
}
