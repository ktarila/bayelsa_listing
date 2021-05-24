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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'help' => 'title of advert',
            ])
            ->add('fullname')
            ->add('phone')
            ->add('email')
            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 6, 'placeholder' => 'Enter advert description'],
            ])
            ->add('state')
            ->add('category')
            ->add('lga')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
