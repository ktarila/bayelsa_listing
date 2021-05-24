<?php

namespace App\Controller\Admin;

use App\Entity\Lga;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LgaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lga::class;
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
