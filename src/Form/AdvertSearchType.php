<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('state', EntityType::class, [
                'expanded' => false,
                'multiple' => false,
                'class' => State::class,
                'required' => false,
                'attr' => ['name' => 'state'],
            ])
            // ->add('lga')
            ->add('category', EntityType::class, [
                'expanded' => true,
                'multiple' => true,
                'class' => Category::class,
                'required' => false,
                'attr' => ['name' => 'category'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
