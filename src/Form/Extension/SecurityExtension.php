<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityExtension extends AbstractTypeExtension
{
    private $authorizationChecker;
    private $propertyAccessor;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, PropertyAccessorInterface $propertyAccessor)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Returns an array of extended types.
     */
    public static function getExtendedTypes(): iterable
    {
        // return [FormType::class] to modify (nearly) every field in the system
        return [FormType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['is_granted_attribute']) {
            return;
        }

        if ($options['is_granted_disabled']) {
            return;
        }

        if ($options['is_granted_hide']) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $form = $event->getForm();

                if ($this->isGranted($options, $form)) {
                    return;
                }

                $form->getParent()->remove($form->getName());
            });
        } else {
            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
                $form = $event->getForm();

                if ($this->isGranted($options, $form)) {
                    return;
                }

                $event->setData($form->getViewData());
            });
        }
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['is_granted_disabled']) {
            return;
        }

        if ($this->isGranted($options, $form)) {
            return;
        }

        $this->disableView($view);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'is_granted_attribute' => null,
            'is_granted_subject_path' => null,
            'is_granted_hide' => false,
            'is_granted_disabled' => false,
        ]);
    }

    public function getExtendedType()
    {
        return FormType::class;
    }

    private function isGranted(array $options, FormInterface $form)
    {
        if (!$options['is_granted_attribute']) {
            return true;
        }

        $subject = null;

        if ($options['is_granted_subject_path']) {
            $subject = $this->propertyAccessor->getValue($form, $options['is_granted_subject_path']);
        }

        if ($this->authorizationChecker->isGranted($options['is_granted_attribute'], $subject)) {
            return true;
        }

        return false;
    }

    private function disableView(FormView $view)
    {
        $view->vars['attr']['disabled'] = true;

        foreach ($view as $child) {
            $this->disableView($child);
        }
    }
}
