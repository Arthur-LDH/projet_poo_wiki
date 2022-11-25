<?php

namespace App\Controller\Admin;

use App\Entity\Console;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
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
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('consoleImgFile')->setFormType(VichImageType::class)->hideOnIndex(),
            ImageField::new('img')->setBasePath('build/images/console_img/')->onlyOnIndex()
        ];
    }
}
