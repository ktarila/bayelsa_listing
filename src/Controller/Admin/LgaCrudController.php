<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller\Admin;

use App\Entity\Lga;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LgaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lga::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Lga')
            ->setEntityLabelInPlural('Lga')
            ->setSearchFields(['id', 'name'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $state = AssociationField::new('state');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $state];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $state];
        }
        if (Crud::PAGE_NEW === $pageName) {
            return [$name, $state];
        }
        if (Crud::PAGE_EDIT === $pageName) {
            return [$name, $state];
        }
    }
}
