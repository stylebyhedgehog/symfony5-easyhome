<?php

namespace App\Controller\Admin;

use App\Entity\PersonalData;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PersonalDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PersonalData::class;
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
