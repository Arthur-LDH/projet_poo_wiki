<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('img')->setBasePath('build/images/article_img/')->onlyOnIndex(),
            TextField::new('name'),
            SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex(),
            TextareaField::new('description'),
            AssociationField::new('author')->autocomplete(),
            AssociationField::new('console')->autocomplete()->hideOnIndex(),
            ArrayField::new('console')->onlyOnIndex(),
            AssociationField::new('licence')->autocomplete(),
            TextField::new('articleImgFile')->setFormType(VichImageType::class)->hideOnIndex(),
            TextEditorField::new('content'),
            BooleanField::new('state', label: "Publié"),
            BooleanField::new('moderated', label: "Relus"),
        ];
    }
}
