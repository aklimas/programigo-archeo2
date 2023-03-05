<?php

namespace App\Form\Core\EmailTemplate;

use App\Entity\Core\EmailTemplate\EmailTemplate;
use App\Entity\Core\EmailTemplate\EmailTemplateStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Nazwa szablonu',
                'attr' => [
                    'class' => 'form-control form-control-sm',
                ],
            ])
            ->add('subject', TextType::class, [
                'required' => true,
                'label' => 'Temat email',
                'attr' => [
                    'class' => 'form-control form-control-sm',
                ],
            ])
/*            ->add('content', TextareaType::class, [
                'required' => true, 'label' => 'Treść emaila',
                'attr' => [
                    'class' => 'form-control form-control-sm ',
                    'rows' => 15,
                ],
                'help' => 'Dostępne pola: [imie] [nr_zgloszenia] [termin_realizacji] [kwota] [komenentarz_planosc] [stopka]', ])*/
            ->add('status', EntityType::class, [
                'class' => EmailTemplateStatus::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.name', 'ASC');
                },
    'choice_attr' => function (EmailTemplateStatus $ticketsStatus) {
        return ['style' => 'background-color: '.$ticketsStatus->getColor().'; color: '.$ticketsStatus->getColorText().';'];
    },
                'choice_label' => 'name',
                'label' => 'Status',
                'required' => true,
                'attr' => ['autocomplete' => 'off', 'class' => 'form-select-sm'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmailTemplate::class,
        ]);
    }
}
