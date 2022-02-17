<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextareaField::new('content'),
            TextField::new('buttonTitle'),
            TextField::new('buttonUrl'),
            ImageField::new('cover')
                ->setUploadDir('public/uploads/files')
                ->setBasePath('uploads/files')
                ->setFormTypeOptions(['required' => false])
                ->setUploadedFileNamePattern('[contenthash].[extension]')
                ->setRequired(false),
        ];
    }

}
