<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Field\SubscriptionSchemaField;
use App\Entity\Subscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionCrudController extends AbstractCrudController
{

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ;
    }

    public static function getEntityFqcn(): string
    {
        return Subscription::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('type')->setFormTypeOption('disabled','disabled')->setSortable(false),
            SubscriptionSchemaField::new('infoAsObject')
                ->setLabel('Настройки')
                ->setRequired(true)->formatValue(function ($values) {

                    $r = '';
                    foreach ($values as $key => $value) {
                        $r .= $key . ': ' . $value . '; ';
                    }
                    return mb_substr($r, 0, 200) . (mb_strlen($r) > 200 ? '...' : '');
                })->setSortable(false)
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions(['validation_groups' => ['Default', 'creation']])->setSearchFields(null);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
//                var_dump($entityInstance->getInfo());exit;
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}
