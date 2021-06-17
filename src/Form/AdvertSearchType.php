<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Form;

use App\Entity\AdType;
use App\Entity\Category;
use App\Entity\State;
use Doctrine\ORM\EntityRepository;
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
            ->add('type', EntityType::class, [
                'expanded' => true,
                'multiple' => false,
                'class' => AdType::class,
                'label' => 'Advert Type',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $category = $options['adtype'];
                    if ('buy' === $category) {
                        return $er->createQueryBuilder('c')
                            ->andWhere('c.name = :buy')
                            ->setParameter('buy', 'buy')
                            ->orderBy('c.id', 'ASC')
                            ;
                    }
                    if ('sell' === $category) {
                        return $er->createQueryBuilder('c')
                            ->andWhere('c.name != :buy')
                            ->setParameter('buy', 'buy')
                            ->orderBy('c.id', 'ASC')
                            ;
                    }

                    return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC')
                    ;
                },
                'required' => false,
                'attr' => ['name' => ''],
            ])
            ->add('category', EntityType::class, [
                'expanded' => true,
                'multiple' => true,
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC')
                    ;
                },
                'required' => false,
                'attr' => ['name' => 'category'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'adtype' => null,
            'category' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
