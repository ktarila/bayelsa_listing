<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');
        $displayName = TextField::new('displayName');
        $slug = TextField::new('slug');
        $categoryImage = TextareaField::new('imageFile')->setFormType(VichImageType::class)->setLabel('Category Image');
        $active = BooleanField::new('active');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $displayName, $slug, $active];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $displayName, $slug, $categoryImage];
        }
        if (Crud::PAGE_NEW === $pageName) {
            return [$name, $displayName, $categoryImage];
        }
        if (Crud::PAGE_EDIT === $pageName) {
            return [$name, $displayName, $categoryImage];
        }
    }
}
