<?php

namespace App\Form\Core\SmsTemplate;

use App\Entity\Core\SmsTemplate\SmsTemplate;
use App\Entity\Core\SmsTemplate\SmsTemplateStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SmsTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['required' => true, 'label' => 'Nazwa szablonu', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('content', TextareaType::class, ['required' => true, 'label' => 'Treść sms', 'attr' => ['class' => 'form-control form-control-sm ']])
            ->add('status', EntityType::class, [
                'class' => SmsTemplateStatus::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.name', 'ASC');
                },
                'choice_attr' => function (SmsTemplateStatus $ticketsStatus) {
                    return ['style' => 'background-color: '.$ticketsStatus->getColor().'; color: '.$ticketsStatus->getColorText().';'];
                },
                'choice_label' => 'name',
                'label' => 'Status',
                'required' => true,
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control-sm'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SmsTemplate::class,
        ]);
    }
}
