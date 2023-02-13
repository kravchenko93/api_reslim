<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\DishService;
use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\UserDishChoicePerDate;
use App\Repository\UserDishChoicePerDateRepository;
use App\Repository\UserRepository;
use App\Repository\DishRepository;

class SetUserDishChoiceCommand extends Command
{
    /**
     * @var DishService
     */
    private $dishService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var UserDishChoicePerDateRepository
     */
    private $userDishChoicePerDateRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var DishRepository
     */
    private $dishRepository;

    protected static $defaultName = 'app:user_dish_choice_planned:set';

    /**
     * @param DishService $dishService
     * @param UserService $userService
     * @param UserDishChoicePerDateRepository $userDishChoicePerDateRepository
     * @param UserRepository $userRepository
     * @param DishRepository $dishRepository
     */
    public function __construct(
        DishService $dishService,
        UserService $userService,
        UserDishChoicePerDateRepository $userDishChoicePerDateRepository,
        UserRepository $userRepository,
        DishRepository $dishRepository
    ) {
        $this->dishService = $dishService;
        $this->userService = $userService;
        $this->userDishChoicePerDateRepository = $userDishChoicePerDateRepository;
        $this->userRepository = $userRepository;
        $this->dishRepository = $dishRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new \DateTimeImmutable('now');

        foreach ($this->userService->getUsers() as $user) {
            $dishesForUser = $this->dishService->getDishesForUser($user->getId());

            foreach ($dishesForUser as $dishesByCategory) {
                $dish = $dishesByCategory->getDishes()[0];

                $userDishChoicesPerDate = $this->userDishChoicePerDateRepository->findByUserAndDishCategoryByDate($user->getId(), $dishesByCategory->getCategory()->getId(), $date);

                //если уже есть блюдо с такой категорией
                if (!empty($userDishChoicesPerDate)) {
                    continue;
                }

                $userEntity = $this->userRepository->find($user->getId());
                $dishEntity = $this->dishRepository->find($dish->getId());

                $userDishChoicePerDate = new UserDishChoicePerDate();
                $userDishChoicePerDate->setDish($dishEntity);
                $userDishChoicePerDate->setUser($userEntity);
                $userDishChoicePerDate->setDate(new \DateTime($date->format('Y-m-d')));
                $userDishChoicePerDate->setInFact(false);

                $em = $this->userDishChoicePerDateRepository->getEm();
                $em->persist($userDishChoicePerDate);
                $em->flush($userDishChoicePerDate);
            }
        }

        return 0;
    }
}