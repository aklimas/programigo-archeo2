<?php

namespace App\Form\Core\Messenger;

use App\Entity\Core\Messenger\Messenger;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class NewMessageType extends AbstractType
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setAction($this->router->generate('messenger_newMessage'))
            ->add('userReceipt', EntityType::class, [
            'class' => User::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('m')
                    ->leftJoin('m.userData', 's')
                    ->orderBy('s.name', 'ASC');
            },
            'choice_label' => function (User $user) {
                if ($user->getUserData()) {
                    return $user->getUserData()->getFullName();
                } else {
                    return '';
                }
            },
            'label' => 'Użytkownik',
            'required' => false,
            'empty_data' => null,
            'placeholder' => ' -- wybierz użytkownika --',
            'attr' => ['autocomplete' => 'off', 'class' => 'form-control-sm'],
        ])
            ->add('content', TextareaType::class, ['required' => true, 'label' => 'Treść wiadomości', 'attr' => ['class' => 'form-control form-control-sm ', 'rows' => 10]])
            ->add('submit', SubmitType::class, ['label' => 'Wyślij', 'attr' => ['class' => 'sendMessage mt-4 btn btn-primary btn-sm']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messenger::class,
            'attr' => ['id' => 'newMessage'],
        ]);
    }
}
