<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\VichImageField;
use App\Entity\Dish;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Enum\DishVitaminEnum;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use App\Admin\Type\DishIngredientType;
use App\Admin\Type\DishStepType;

class DishCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Dish::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setRequired(true)->setSortable(false),
            ImageField::new('image')->setBasePath($this->getParameter('app.path.dish_images'))->onlyOnIndex()->setSortable(false),
            VichImageField::new('imageFile', 'Image')->onlyWhenCreating()->setRequired(true),
            VichImageField::new('imageFile', 'Image')->onlyWhenUpdating()->setRequired(false),
            TextEditorField::new('description')->setRequired(true)->setRequired(true)->hideOnIndex(),
            AssociationField::new('dishCategory', 'Category')->setRequired(true),
            BooleanField::new('hide', 'Hide')->setSortable(false)->setLabel('Скрыть'),
            TextEditorField::new('cookingTools')->setRequired(true)->setRequired(true)->hideOnIndex()->setLabel('Вам понадобится'),
            IntegerField::new('weight')->setRequired(true)->setRequired(true)->hideOnIndex()->setLabel('Вес гр.'),
            IntegerField::new('proteins')->setRequired(true)->setRequired(true)->hideOnIndex()->setLabel('Белки'),
            IntegerField::new('fats')->setRequired(true)->setRequired(true)->hideOnIndex()->setLabel('Жиры'),
            IntegerField::new('carbohydrates')->setRequired(true)->setRequired(true)->hideOnIndex()->setLabel('Углеводы'),
            TextField::new('cookingTime')->setRequired(true)->setRequired(true)->hideOnIndex()->setLabel('Время готовки'),
            TextField::new('complexity')->setRequired(true)->setRequired(true)->hideOnIndex()->setLabel('Сложность'),
            ChoiceField::new('vitamins', 'Vitamins')
                ->allowMultipleChoices()
                ->setChoices(DishVitaminEnum::ALL)->hideOnIndex()->setRequired(false),
            CollectionField::new('dishIngredients', 'Ingredients')->setEntryType(DishIngredientType::class)->allowAdd(true)->setSortable(false)->setRequired(true),
            CollectionField::new('dishSteps', 'Steps')->setEntryType(DishStepType::class)->allowAdd(true)->setSortable(false)->setRequired(true),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions(['validation_groups' => ['Default', 'creation']]);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /**
         * @var Dish $entityInstance
         */
        $entityManager->persist($entityInstance);

        foreach ($entityInstance->getDishSteps() as $dishStep) {
            $dishStep->setDish($entityInstance);
        }

        foreach ($entityInstance->getDishIngredients() as $dishIngredient) {
            $dishIngredient->setDish($entityInstance);
        }

        $entityManager->flush();
    }
}
