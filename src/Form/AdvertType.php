<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Form;

use App\Entity\Advert;
use App\Entity\Lga;
use App\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class AdvertType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        if (null === $user) {
            $builder->add('fullname', null, [
                'label' => false,
                'attr' => ['placeholder' => 'Name*'],
            ])

                ->add('email', null, [
                    'label' => false,
                    'attr' => ['placeholder' => 'Email*'],
                ])
        ;
        }

        $builder
            ->add('phone', null, [
                'label' => false,
                'attr' => ['placeholder' => 'Phone*'],
            ])

            ->add('title', null, [
                'label' => false,
                'attr' => ['placeholder' => 'Title of advert'],
            ])

            ->add('description', TextareaType::class, [
                'label' => false,
                'attr' => ['rows' => 6, 'placeholder' => 'Enter advert description'],
            ])
            ->add('address', null, [
                'label' => false,
                'attr' => ['placeholder' => 'Street Address'],
            ])
            ->add('state', null, ['placeholder' => 'Choose State'])
            ->add('category')
            ->add('photo', FileType::class, [
                'attr' => ['data-mydropzone-target' => 'input'],
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'label' => 'Advert Images',
            ])
            ->add('uploadToken', null, [
                'label' => false,
                'attr' => ['class' => 'hidden'], ])
        ;

        $formModifier = function (FormInterface $form, State $state = null) {
            $lgas = null === $state ? [] : $state->getLgas();

            $form->add('lga', EntityType::class, [
                'class' => Lga::class,
                'required' => true,
                'choices' => $lgas,
                'label' => 'City/Town',
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getState());
            }
        );

        $builder->get('state')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $state = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $state);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
