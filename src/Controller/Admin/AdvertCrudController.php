<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller\Admin;

use App\Entity\Advert;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdvertCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Advert::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new', 'show')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $title = TextField::new('title');
        $type = AssociationField::new('type');
        $category = AssociationField::new('category');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $title, $type, $category];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $title, $type, $category];
        }
        if (Crud::PAGE_NEW === $pageName) {
            return [$title, $type, $category];
        }
        if (Crud::PAGE_EDIT === $pageName) {
            return [$title, $category, $type];
        }
    }
}
