<?php

namespace App\Form\Other\Settlement;

use App\Entity\Other\Settlement\Settlement;
use App\Entity\Other\Settlement\SettlementStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettlementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['required' => true, 'label' => 'Tytuł', 'attr' => ['class' => 'form-control form-control-sm']])
            ->add('content', TextareaType::class, ['required' => false, 'label' => 'Zawartość', 'attr' => ['class' => 'form-control form-control-sm ']])

            // TODO Pliki
            /*->add('file', FileType::class, [
                'label_attr' => ['class' => 'form-label'],
                'label' => false,
                'attr' => ['class' => 'file', 'id' => 'upload_file'],
                'mapped' => false,
                'multiple' => false,
                'required' => false,
                'constraints' => [
                    /*new File([
                        'maxSize' => '100000k',
                        'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
                            'text/x-comma-separated-values',
                        ],
                        'mimeTypesMessage' => 'Nieprawidłowy typ pliku',
                    ]),
                ],
            ])*/

            // TODO Status
            /*->add('status', EntityType::class, [
                'class' => SettlementStatus::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.name', 'ASC');
                },
                'choice_label' => 'name',
                'label' => 'Status',
                'required' => true,
                'attr' => ['autocomplete' => 'off', 'class' => 'form-control-sm'],
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settlement::class,
        ]);
    }
}
