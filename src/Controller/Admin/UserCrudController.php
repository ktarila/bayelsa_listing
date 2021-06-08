<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'email', 'roles', 'username', 'phone', 'fullname'])
            ->setPaginatorPageSize(10)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new', 'show')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id', 'ID');
        $email = TextField::new('email');
        $phone = TextField::new('phone');
        $active = BooleanField::new('active');
        $roles = ArrayField::new('roles');
        $fullname = TextField::new('fullname');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$fullname, $email, $phone, $active];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $email, $roles,  $phone, $active, $fullname];
        }
        if (Crud::PAGE_NEW === $pageName) {
            return [$fullname, $email, $phone, $active, $roles];
        }
        if (Crud::PAGE_EDIT === $pageName) {
            return [$fullname, $email, $phone, $active, $roles];
        }
    }
}
