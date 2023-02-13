<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\Dishes\{DishCategoryResponseDto,
    DishesWithCategoryResponseDto,
    DishIngredientResponseDto,
    DishResponseDto,
    DishStepResponseDto,
    UpdateDishRatingRequestDto};
use App\Entity\Dish;
use App\Entity\DishIngredient;
use App\Entity\DishStep;
use App\Entity\UserDishChoicePerDate;
use App\Entity\UserDishExcludedPerDate;
use App\Entity\UserDishRating;
use App\Exception\SystemException;
use App\Repository\DishRepository;
use App\Repository\UserDishChoicePerDateRepository;
use App\Repository\UserDishExcludedPerDateRepository;
use App\Repository\UserDishRatingRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DishService
{
    /**
     * @var DishRepository
     */
    private $dishRepository;

    /**
     * @var UserDishRatingRepository
     */
    private $userDishRatingRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ValidatorService
     */
    private $validatorService;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var UserDishExcludedPerDateRepository
     */
    private $userDishExcludedPerDateRepository;

    /**
     * @var UserDishChoicePerDateRepository
     */
    private $userDishChoicePerDateRepository;

    /**
     * @param DishRepository $dishRepository
     * @param ValidatorService $validatorService
     * @param UserDishRatingRepository $userDishRatingRepository
     * @param UserRepository $userRepository
     * @param ParameterBagInterface $params
     * @param UserDishExcludedPerDateRepository $userDishExcludedPerDateRepository
     */
    public function __construct(
        DishRepository $dishRepository,
        ValidatorService $validatorService,
        UserDishRatingRepository $userDishRatingRepository,
        UserRepository $userRepository,
        ParameterBagInterface $params,
        UserDishExcludedPerDateRepository $userDishExcludedPerDateRepository,
        UserDishChoicePerDateRepository $userDishChoicePerDateRepository

    )
    {
        $this->dishRepository = $dishRepository;
        $this->validatorService = $validatorService;
        $this->userDishRatingRepository = $userDishRatingRepository;
        $this->userRepository = $userRepository;
        $this->params = $params;
        $this->userDishExcludedPerDateRepository = $userDishExcludedPerDateRepository;
        $this->userDishChoicePerDateRepository = $userDishChoicePerDateRepository;
    }

    /**
     * @param int $userId
     * @return DishesWithCategoryResponseDto[]|array
     */
    public function getDishesForUser(int $userId): array
    {
        $dishes = $this->dishRepository->findDishesForUserPerDateOrderByLogic(new \DateTimeImmutable('now'), $userId);

        $userDishRatings = $this->userDishRatingRepository->findBy(['user' => $userId]);

        /**
         * @var UserDishRating[] $userDishRatings
         */
        $userRatingsByDishId = [];

        foreach ($userDishRatings as $userDishRating) {
            $userRatingsByDishId[$userDishRating->getDish()->getId()] = $userDishRating;
        }

        /**
         * @var Dish[][] $dishesByCategory
         */
        $dishesByCategory = [];

        foreach ($dishes as $dish) {
            $dishCategoryId = $dish->getDishCategory()->getId();
            if (empty($dishesByCategory[$dishCategoryId])) {
                $dishesByCategory[$dishCategoryId] = [];
            }
            $dishesByCategory[$dishCategoryId][] = $dish;
        }

        $res = [];

        foreach ($dishesByCategory as $dishes) {
            $category = $dishes[0]->getDishCategory();

            $res[] = new DishesWithCategoryResponseDto(
                new DishCategoryResponseDto(
                    $category->getName(),
                    $category->getId()
                ),
                array_map(
                    function (Dish $dish) use ($userRatingsByDishId) {
                        $rating = !empty($userRatingsByDishId[$dish->getId()]) ? $userRatingsByDishId[$dish->getId()]->getRating() : null;
                        return new DishResponseDto(
                            $dish->getId(),
                            $dish->getName(),
                            $this->params->get('app.path.dish_images') . '/' . $dish->getImage(),
                            $dish->getDescription(),
                            array_map(function (DishIngredient $dishIngredient) {
                                return new DishIngredientResponseDto(
                                    $dishIngredient->getIngredient()->getName(),
                                    $this->params->get('app.path.ingredient_images') . '/' . $dishIngredient->getIngredient()->getImage(),
                                    $dishIngredient->getQuantity()
                                );
                            }, $dish->getDishIngredients()->toArray()),
                            array_map(function (DishStep $dishStep) {
                                return new DishStepResponseDto(
                                    $dishStep->getText(),
                                    $this->params->get('app.path.dish_step_images') . '/' . $dishStep->getImage()
                                );
                            }, $dish->getDishSteps()->toArray()),
                            $rating,
                            $dish->getCookingTools(),
                            $dish->getWeight(),
                            $dish->getCookingTime(),
                            $dish->getComplexity(),
                            $dish->getProteins(),
                            $dish->getFats(),
                            $dish->getCarbohydrates(),
                            $dish->getVitamins()
                        );
                    },
                    $dishes
                )
            );
        }

        return $res;
    }


    /**
     * @param int $userId
     * @param int $dishId
     * @param UpdateDishRatingRequestDto $updateDishRatingRequest
     */
    public function updateDishRating(int $userId, int $dishId, UpdateDishRatingRequestDto $updateDishRatingRequest)
    {
        $this->validatorService->validateDto($updateDishRatingRequest);

        $userDishRating = $this->userDishRatingRepository->findOneBy(['dish' => $dishId, 'user' => $userId]);

        if (null === $userDishRating) {
            $userDishRating = new UserDishRating();
            $dish = $this->dishRepository->find($dishId);
            if (null === $dish) {
                throw new SystemException('Блюдо не найдено');
            }
            $user = $this->userRepository->find($userId);
            $userDishRating->setDish($dish);
            $userDishRating->setUser($user);
        }

        $userDishRating->setRating($updateDishRatingRequest->getRating());

        $em = $this->userDishRatingRepository->getEm();
        $em->persist($userDishRating);
        $em->flush($userDishRating);
    }

    /**
     * @param int $userId
     * @param int $dishId
     * @param DateTimeImmutable $date
     */
    public function excludeDishForUserPerDate(int $userId, int $dishId, DateTimeImmutable $date)
    {
        /**
         * @var int|null $categoryIdNeedToWriteNewUserDishChoice
         */
        $categoryIdNeedToWriteNewUserDishChoice = null;

        $userDishExcludedPerDate = $this->userDishExcludedPerDateRepository->findOneBy(['dish' => $dishId, 'user' => $userId, 'date' => $date]);

        if (null === $userDishExcludedPerDate) {
            $dish = $this->dishRepository->find($dishId);
            if (null === $dish) {
                throw new SystemException('Блюдо не найдено');
            }

            $userDishChoices = $this->userDishChoicePerDateRepository->findByUserAndDishByDate($userId, $dishId, $date);

            foreach ($userDishChoices as $userDishChoice) {
                if ($userDishChoice->getDish() === $dish) {
                    if (true === $userDishChoice->getInFact()) {
                        throw new SystemException('Блюдо не может быть исключено, оно уже выбрано пользователем на эту дату');
                    } else {
                        //если блюдо было выбрано скриптом, а не через апи, мы удалем выбор и записываем новый
                        // (предполагется что скрипт запускается только один раз)
                        $categoryIdNeedToWriteNewUserDishChoice = $userDishChoice->getDish()->getDishCategory()->getId();

                        $em = $this->userDishChoicePerDateRepository->getEm();
                        $em->remove($userDishChoice);
                        $em->flush();
                    }
                }
            }

            $user = $this->userRepository->find($userId);

            $userDishExcludedPerDate = new UserDishExcludedPerDate();
            $userDishExcludedPerDate->setDish($dish);
            $userDishExcludedPerDate->setUser($user);
            $userDishExcludedPerDate->setDate(new \DateTime($date->format('Y-m-d')));

            $em = $this->userDishExcludedPerDateRepository->getEm();
            $em->persist($userDishExcludedPerDate);
            $em->flush($userDishExcludedPerDate);

            if (null !== $categoryIdNeedToWriteNewUserDishChoice) {
                $dishesForUser = $this->getDishesForUser($userId);

                foreach ($dishesForUser as $dishesByCategory) {
                    if ($categoryIdNeedToWriteNewUserDishChoice === $dishesByCategory->getCategory()->getId()) {
                        $dish = $this->dishRepository->find($dishesByCategory->getDishes()[0]->getId());

                        $userDishChoicePerDate = new UserDishChoicePerDate();
                        $userDishChoicePerDate->setDish($dish);
                        $userDishChoicePerDate->setUser($user);
                        $userDishChoicePerDate->setDate(new \DateTime($date->format('Y-m-d')));
                        $userDishChoicePerDate->setInFact(false);

                        $em = $this->userDishChoicePerDateRepository->getEm();
                        $em->persist($userDishChoicePerDate);
                        $em->flush($userDishChoicePerDate);

                        break;
                    }
                }
            }
        }
    }

    /**
     * @param int $userId
     * @param int $dishId
     * @param DateTimeImmutable $date
     */
    public function setDishChoiceForUserPerDate(int $userId, int $dishId, DateTimeImmutable $date)
    {
        $dish = $this->dishRepository->find($dishId);
        if (null === $dish) {
            throw new SystemException('Блюдо не найдено');
        }

        $allowedDishes = $this->dishRepository->findDishesForUserPerDateOrderByLogic($date, $userId);

        $dishIsAllowed = false;

        foreach ($allowedDishes as $allowedDish) {
            if ($allowedDish === $dish) {
                $dishIsAllowed = true;
                break;
            }
        }

        if (false === $dishIsAllowed) {
            throw new SystemException('Блюдо недоступно для употребления пользователем');
        }

        $userDishChoicesPerDate = $this->userDishChoicePerDateRepository->findByUserAndDishCategoryByDate($userId, $dish->getDishCategory()->getId(), $date);

        $userDishChoicePerDate = null;

        foreach ($userDishChoicesPerDate as $userDishChoicePerDate) {
            if ($dish === $userDishChoicePerDate->getDish()) {
                break;
            } else {
                if (true === $userDishChoicePerDate->getInFact()) {
                    throw new SystemException('Блюдо с такой категории уже было выбрано пользователем');
                } else {
                    //если автоматически проставили выбор блюда (скрипт а не апи), то меняем блюдо без ошибки
                    $userDishChoicePerDate->setDish($dish);
                    break;
                }
            }
        }

        if (null === $userDishChoicePerDate) {
            $userDishChoicePerDate = new UserDishChoicePerDate();
            $user = $this->userRepository->find($userId);
            $userDishChoicePerDate->setDish($dish);
            $userDishChoicePerDate->setUser($user);
            $userDishChoicePerDate->setDate(new \DateTime($date->format('Y-m-d')));
        }

        $userDishChoicePerDate->setInFact(true);

        $em = $this->userDishChoicePerDateRepository->getEm();
        $em->persist($userDishChoicePerDate);
        $em->flush($userDishChoicePerDate);
    }
}