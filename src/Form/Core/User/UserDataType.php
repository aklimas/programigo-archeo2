<?php

namespace App\Form\Core\User;

use App\Entity\Core\UserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paycheckProcent', TextType::class, [
                'required' => true,
                'is_granted_attribute' => 'ROLE_SUPER_ADMIN',
                //'is_granted_hide' => true,
                'label' => 'Prowizja',
                'attr' => [
                    'class' => 'form-control form-control-sm'
                ],
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'Kolor użytkownika',
                'label_attr' => ['class' => 'control-label', 'style' => 'margin-top:7px;'],
                'choices' => [
                    'Lawenda' => 1,
                    'Szałwia' => 2,
                    'Winogorna' => 3,
                    'Flaming' => 4,
                    'Banan' => 5,
                    'Mandarynka' => 6,
                    'Paw' => 7,
                    'Grafit' => 8,
                    'Jagoda' => 9,
                    'Bazylia' => 10,
                    'Pomidor' => 11,
                ],
                'choice_attr' => [
                    'Lawenda' => ['style' => 'background-color: #7986cb; color: #ffffff;'], //1
                    'Szałwia' => ['style' => 'background-color: #33b679; color: #ffffff;'],//2
                    'Winogorna' => ['style' => 'background-color: #8e24aa; color: #ffffff;'],//3
                    'Flaming' => ['style' => 'background-color: #ff887c; color: #ffffff;'],//4
                    'Banan' => ['style' => 'background-color: #f6bf26; color: #ffffff;'],//5
                    'Mandarynka' => ['style' => 'background-color: #f4511e; color: #ffffff;'],//6
                    'Paw' => ['style' => 'background-color: #039be5; color: #ffffff;'],//7
                    'Grafit' => ['style' => 'background-color: #616161; color: #ffffff;'],//8
                    'Jagoda' => ['style' => 'background-color: #3f51b5; color: #ffffff;'],//9
                    'Bazylia' => ['style' => 'background-color: #0b8043; color: #ffffff;'],//10
                    'Pomidor' => ['style' => 'background-color: #dc2127; color: #ffffff;'],//11
                ],
                'multiple' => false,
            ])

            ->add('description', TextareaType::class, ['required' => false, 'label' => 'Krótki opis', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('name', TextType::class, ['required' => true, 'label' => 'Imię', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('lastName', TextType::class, ['required' => true, 'label' => 'Nazwisko', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('phone', TextType::class, ['required' => false, 'label' => 'Telefon', 'attr' => ['class' => 'form-control form-control-sm']])

            ->add('company', TextType::class, ['required' => false, 'label' => 'Nazwa firmy', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('nip', TextType::class, ['required' => false, 'label' => 'NIP', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('regon', TextType::class, ['required' => false, 'label' => 'Regon', 'attr' => ['class' => 'form-control form-control-sm']])

            ->add('street', TextType::class, ['required' => false, 'label' => 'Ulica', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('homeNumber', TextType::class, ['required' => false, 'label' => 'Nr domu', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('apartmentNumber', TextType::class, ['required' => false, 'label' => 'Nr mieszkania', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('postcode', TextType::class, ['required' => false, 'label' => 'Kod', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('city', TextType::class, ['required' => false, 'label' => 'Miejscowość', 'attr' => ['class' => 'form-control form-control-sm']])

            ->add('social_Facebook', TextType::class, ['required' => false, 'label' => 'Facebook', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('social_Twitter', TextType::class, ['required' => false, 'label' => 'Twitter', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('social_Linkedin', TextType::class, ['required' => false, 'label' => 'Linkedin', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('social_Youtube', TextType::class, ['required' => false, 'label' => 'Youtube', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('social_Instagram', TextType::class, ['required' => false, 'label' => 'Instagram', 'attr' => ['class' => 'form-control form-control-sm']])

            ->add('submit', SubmitType::class, ['label' => 'Zapisz', 'attr' => ['class' => 'btn btn-primary btn-sm']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserData::class,
        ]);
    }
}
