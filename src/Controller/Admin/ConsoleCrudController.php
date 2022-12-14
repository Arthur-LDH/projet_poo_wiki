<?php

namespace App\Controller\Admin;

use App\Entity\Console;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ConsoleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Console::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id')->hideOnForm(),
            ImageField::new('img')->setBasePath('build/images/console_img/')->onlyOnIndex(),
            TextField::new('name'),
            TextEditorField::new('description')->hideOnIndex(),
            DateField::new('release_date'),
            SlugField::new('slug')->setTargetFieldName('name')->setUnlockConfirmationMessage("Il est recommandé d'utiliser des slugs automatiques")->hideOnIndex(),
            TextField::new('consoleImgFile')->setFormType(VichImageType::class)->hideOnIndex(),
        ];
    }
}
