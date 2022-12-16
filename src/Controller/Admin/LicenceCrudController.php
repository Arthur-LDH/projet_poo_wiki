<?php

namespace App\Controller\Admin;

use App\Entity\Licence;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LicenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Licence::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // IdField::new('id')->hideOnForm(),
            ImageField::new('img')->setBasePath('build/images/licence_img/')->onlyOnIndex(),
            TextField::new('name'),
            TextareaField::new('description'),
            SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex(),
            TextField::new('licenceImgFile')->setFormType(VichImageType::class)->hideOnIndex(),           
        ];
    }
}
