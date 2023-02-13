<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\UserDishChoicePerDate;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class UserDishChoiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserDishChoicePerDate::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
//            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setSearchFields(null);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateField::new('date')->setSortable(true),
            AssociationField::new('dish')->setSortable(false),
            AssociationField::new('user')->setSortable(false),
            BooleanField::new('inFact')->setSortable(false)->renderAsSwitch(false)
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('date')
            ->add('user');
    }

}
