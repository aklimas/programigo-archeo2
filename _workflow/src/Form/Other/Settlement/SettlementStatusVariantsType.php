<?php

namespace App\Form\Other\Settlement;

use App\Entity\Other\Settlement\SettlementStatusVariants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettlementStatusVariantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', TextType::class, ['required' => true, 'label' => 'Rola', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('name', TextType::class, ['required' => true, 'label' => 'Nazwa', 'attr' => ['class' => 'form-control form-control-sm']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SettlementStatusVariants::class,
        ]);
    }
}
