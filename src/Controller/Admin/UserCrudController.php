<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\UserRoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Admin\Field\SchemaField;
use App\Service\JsonSchemaService;
use App\Enum\JsonSchemaNameEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class UserCrudController extends AbstractCrudController
{
    /**
     * @var UserPasswordEncoderInterface
     * @param JsonSchemaService $jsonSchemaService
     */
    private $userPasswordEncoder;

    /**
     * @var JsonSchemaService $jsonSchemaService
     */
    private $jsonSchemaService;



    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder,
        JsonSchemaService $jsonSchemaService
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->jsonSchemaService = $jsonSchemaService;
    }


    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $userQuestionsFormSchema = json_decode($this->jsonSchemaService->getSchemaString(JsonSchemaNameEnum::USER_QUESTIONS_FORM));

        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email')->setRequired(true),
            DateField::new('activeUserSubscription.dateFinish')->setLabel('paid to')->onlyOnIndex(),
            TextField::new('password')->onlyWhenCreating()->setRequired(true),
            TextField::new('password')->onlyWhenUpdating()->setRequired(true),
            SchemaField::new('infoAsObject')
                ->setFormTypeOption('data_schema', $userQuestionsFormSchema)
                ->setLabel('Тест пользователя')
                ->setRequired(true)->formatValue(function ($values) {
                    $r = '';
                    foreach ($values as $key => $value) {
                        $r .= $key . ': ' . $value . '; ';
            }
                    return mb_substr($r, 0, 200) . (mb_strlen($r) > 200 ? '...' : '');
                }),
            ChoiceField::new('roles', 'Roles')
                ->allowMultipleChoices()
                ->autocomplete()
                ->setChoices(UserRoleEnum::ALL)
                ->setRequired(true)
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /**
         * @var User $entityInstance
         */
        $entityInstance->setPassword($this->userPasswordEncoder->encodePassword($entityInstance, $entityInstance->getPassword()));
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /**
         * @var User $entityInstance
         */
        $entityInstance->setPassword($this->userPasswordEncoder->encodePassword($entityInstance, $entityInstance->getPassword()));
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions(['validation_groups' => ['Default', 'creation']]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('userSubscriptions');
    }
}
