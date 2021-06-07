<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Form;

use App\Entity\Advert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
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
            $builder->add('fullname')
                ->add('phone')
                ->add('email')
        ;
        }

        $builder

            ->add('title', null, [
                'help' => 'title of advert',
            ])

            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 6, 'placeholder' => 'Enter advert description'],
            ])
            ->add('address', null, ['label' => 'Street'])
            ->add('state')
            ->add('category')
            ->add('lga')
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
