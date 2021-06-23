<?php

namespace App\Controller\Admin;

use App\Entity\Ducks;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DucksCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ducks::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('firstname'),
            TextField::new('lastname'),
            TextField::new('username'),
            TextField::new('email'),
            IntegerField::new('discord_id'),
        ];
    }

//    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
//    {
//        dd($entityInstance);
//    }

}
