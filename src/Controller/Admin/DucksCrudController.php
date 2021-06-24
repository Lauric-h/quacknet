<?php

namespace App\Controller\Admin;

use App\Entity\Ducks;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

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
            TextField::new('username'),
            TextField::new('email'),
            TextField::new('firstname'),
            TextField::new('lastname'),
            IntegerField::new('discord_id'),
            BooleanField::new('deleted', "Ban/Unban")
        ];
    }

//    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
//    {
//        dd($entityInstance);
//    }
//
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('deleted'));
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setDeleted(1);
        $entityManager->flush();
    }

}
