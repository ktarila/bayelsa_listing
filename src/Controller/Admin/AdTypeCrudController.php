<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller\Admin;

use App\Entity\AdType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdType::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
