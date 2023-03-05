<?php

namespace App\Form\Core\Files;

use App\Entity\Core\Files\Files;
use App\Entity\Core\Files\FilesStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['required' => true, 'label' => 'Tytuł', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('content', TextareaType::class, ['required' => true, 'label' => 'Zawartość', 'attr' => ['class' => 'form-control form-control-sm ']])
            ->add('status', EntityType::class, [
                'class' => FilesStatus::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.name', 'ASC');
                },
                'choice_attr' => function (FilesStatus $ticketsStatus) {
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
            'data_class' => Files::class,
        ]);
    }
}
