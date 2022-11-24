<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    public function configureFields(string $pageName): iterable
    {

        return [
            TextField::new('name'),
            TextField::new('description'),
            TextEditorField::new('content'),
            AssociationField::new('author')->autocomplete(),
            AssociationField::new('console')->autocomplete(),
            AssociationField::new('licence')->autocomplete(),
            DateField::new('date'),
        ];
    }
}
