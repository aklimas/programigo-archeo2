<?php

namespace App\Form\Other\ContactsList;

use App\Entity\Other\ContactsList\ContactsListStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactsListStatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['required' => true, 'label' => 'Podstawowa nazwa statusu', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('nameAction', TextType::class, ['required' => false, 'label' => 'Status - akcja', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('value', TextType::class, ['required' => true, 'label' => 'Nazwa robocza', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('color', TextType::class, ['required' => true, 'label' => 'Kolor statusu - tło', 'attr' => ['class' => 'form-control form-control-sm color-input']])
            ->add('colorText', TextType::class, ['required' => false, 'label' => 'Kolor statusu - tekst', 'empty_data' => '#FFF', 'attr' => ['class' => 'form-control form-control-sm color-input']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactsListStatus::class,
        ]);
    }
}
