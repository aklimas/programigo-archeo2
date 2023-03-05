<?php

namespace App\Form\Core\SmsTemplate;

use App\Entity\Core\SmsTemplate\SmsTemplateStatusVariants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SmsTemplateStatusVariantsType extends AbstractType
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
            'data_class' => SmsTemplateStatusVariants::class,
        ]);
    }
}
