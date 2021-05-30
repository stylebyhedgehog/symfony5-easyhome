<?php

namespace App\Controller\Admin;

use App\Entity\AdImage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdImage::class;
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
