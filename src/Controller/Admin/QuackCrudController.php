<?php

namespace App\Controller\Admin;

use App\Entity\Quack;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class QuackCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quack::class;
    }

    public function configureFilters(Filters $filters): Filters
    {

    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextEditorField::new('content'),
            DateTimeField::new('created_at'),
            TextField::new('duck'),
            TextField::new('parent'),
        ];
    }

        public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setDeleted(1);
        $entityManager->flush();
    }

}
