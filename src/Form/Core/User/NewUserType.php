<?php

namespace App\Form\Core\User;

use App\Entity\User;
use App\Service\HelperService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewUserType extends AbstractType
{
    private HelperService $helper;

    public function __construct(HelperService $helperService)
    {
        $this->helper = $helperService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'mapped' => false,
                'required' => true,
                'label' => '* Imię',
                'attr' => [
                    'autocomplete' => 'disabled',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Proszę wpisać imię ',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'mapped' => false,
                'required' => true,
                'label' => '* Nazwisko',
                'attr' => [
                    'autocomplete' => 'disabled',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Proszę wpisać nazwisko',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => '* Adres Email',
                'attr' => [
                    'autocomplete' => 'new-email',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Proszę wpisać adres e-mail',
                    ]),
                ],
            ])
            ->add('phone', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => '* Nr Telefonu',
                'attr' => [
                    'autocomplete' => 'disabled',
                    'class' => 'form-control',
                ],
            ])
            ->add('password', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Proszę wpisać hasło',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Twoje hasło powinno się składać z {{ limit }} znaków',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => '* Hasło',
                'required' => true,
                'attr' => [
                    'value' => $this->helper->randomString(8),
                    'class' => 'form-control',
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Zapisz', 'attr' => ['class' => 'btn btn-primary w-100 mt-2']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
