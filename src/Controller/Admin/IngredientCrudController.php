<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use App\Admin\Field\VichImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;

class IngredientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ingredient::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setRequired(true),
            ImageField::new('image')->setBasePath($this->getParameter('app.path.ingredient_images'))->onlyOnIndex()->setSortable(false),
            VichImageField::new('imageFile', 'Image')->onlyWhenCreating()->setRequired(true),
            VichImageField::new('imageFile', 'Image')->onlyWhenUpdating()->setRequired(false),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions(['validation_groups' => ['Default', 'creation']]);
    }

    public function delete(AdminContext $context)
    {
        /**
         * @var Ingredient$ingredient
         */
        $ingredient = $context->getEntity()->getInstance();
        if (!empty($ingredient->getDishIngredients()->toArray())) {
            $context->getRequest()->getSession()->getFlashBag()->add('danger', 'Ingredient has associated dishes');
            return $this->redirect($this->get(AdminUrlGenerator::class)->setAction(Action::INDEX)->unset(EA::ENTITY_ID)->generateUrl());
        }

        return parent::delete($context);
    }

}
