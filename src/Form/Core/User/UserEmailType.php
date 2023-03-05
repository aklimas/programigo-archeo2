<?php

namespace App\Form\Core\User;

use App\Entity\Core\UserEmail;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('senderEmail', EmailType::class, ['required' => true, 'label' => 'Adres email', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('senderLabel', TextType::class, ['required' => true, 'label' => 'Nadawca - Tytuł', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('senderPass', TextType::class, ['required' => false, 'label' => 'Hasło', 'data' => '', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('senderHost', TextType::class, ['required' => true, 'label' => 'Host', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('senderPort', TextType::class, ['required' => false, 'label' => 'Port', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('footer', CKEditorType::class, [
                'config' => [
                    'uiColor' => '#ffffff',
                    // ...
                ],
                'required' => false, 'label' => 'Stopka', 'attr' => ['class' => 'form-control form-control-sm'], ])
            ->add('submit', SubmitType::class, ['label' => 'Zapisz', 'attr' => ['class' => 'btn btn-primary btn-sm']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEmail::class,
        ]);
    }
}
