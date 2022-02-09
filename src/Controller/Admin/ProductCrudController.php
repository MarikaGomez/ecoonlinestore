<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            SlugField::new('slug')->setTargetFieldName('name'),
            ImageField::new('image','Product Image')
                ->setUploadDir('public/uploads/files')
                ->setBasePath('uploads/files')
                ->setFormTypeOptions(['required' => false])
                ->setUploadedFileNamePattern('[contenthash].[extension]'),
            ImageField::new('image2','Product Image')
                ->setUploadDir('public/uploads/files')
                ->setBasePath('uploads/files')
                ->setFormTypeOptions(['required' => false])
                ->setUploadedFileNamePattern('[contenthash].[extension]'),
            ImageField::new('image3','Product Image')
                ->setUploadDir('public/uploads/files')
                ->setBasePath('uploads/files')
                ->setFormTypeOptions(['required' => false])
                ->setUploadedFileNamePattern('[contenthash].[extension]'),
            ImageField::new('image4','Product Image')
                ->setUploadDir('public/uploads/files')
                ->setBasePath('uploads/files')
                ->setFormTypeOptions(['required' => false])
                ->setUploadedFileNamePattern('[contenthash].[extension]'),
            TextField::new('brand'),
            TextareaField::new('description'),
            TextareaField::new('composition'),
            MoneyField::new('price')->setCurrency('USD'),
            AssociationField::new('category')
        ];
    }

}
