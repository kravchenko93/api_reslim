<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\Dishes\UpdateDishRatingRequestDto;
use App\Dto\Dishes\DishesWithCategoryResponseDto;
use App\Service\DishService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Swagger\Annotations as SWG;
use App\Exception\SystemException;
use \DateTimeImmutable;

class DishController extends AbstractController
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var DishService
     */
    private $dishService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param SerializerInterface $serializer
     * @param DishService $dishService
     * @param UserService $userService
     */
    public function __construct(
        SerializerInterface $serializer,
        DishService $dishService,
        UserService $userService
    )
    {
        $this->serializer = $serializer;
        $this->dishService = $dishService;
        $this->userService = $userService;
    }

    /**
     * Получить блюда для пользователя на день
     * @Nelmio\Security(name="Bearer")
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ",
     *   @SWG\Items(@Nelmio\Model(type=DishesWithCategoryResponseDto::class))
     * )
     * @return JsonResponse
     *
     */
    public function getDishes(): JsonResponse
    {
        $userId = $this->userService->getAuthUserId();
        $dishes = $this->dishService->getDishesForUser($userId);

        $jsonContent = $this->serializer->serialize($dishes, 'json');

        return new JsonResponse($jsonContent, 200, [], true);
    }

    /**
     * Обновить рейтинг блюда для пользователя
     * @Nelmio\Security(name="Bearer")
     * @SWG\Parameter(
     *     name="body",
     *     type="string",
     *     in="body",
     *     required=true,
     *     @Nelmio\Model(type=UpdateDishRatingRequestDto::class)
     * )
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ"
     * )
     * @param int $dishId
     * @param Request $request
     * @return JsonResponse
     */
    public function setDishRating($dishId, Request $request): JsonResponse
    {
        if (false === is_numeric($dishId)) {
            throw new SystemException('dishId должен быть числом');
        }
        $dishId = (int) $dishId;

        $content = $request->getContent();
        /**
         * @var UpdateDishRatingRequestDto $updateDishRatingRequest
         */
        $updateDishRatingRequest = $this->serializer->deserialize($content, UpdateDishRatingRequestDto::class, 'json');

        $userId = $this->userService->getAuthUserId();
        $this->dishService->updateDishRating($userId, (int) $dishId, $updateDishRatingRequest);

        return new JsonResponse();
    }

    /**
     * Скрыть блюдо для пользователя навсегда
     * @Nelmio\Security(name="Bearer")
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ"
     * )
     * @param int $dishId
     * @return JsonResponse
     */
    public function hideDishForUser($dishId): JsonResponse
    {
        if (false === is_numeric($dishId)) {
            throw new SystemException('dishId должен быть числом');
        }
        $dishId = (int) $dishId;

        //рэйтинг 0 - означает, что пользователь скрыл блюдо навсегда
        $updateDishRatingRequest = new UpdateDishRatingRequestDto(0);

        $userId = $this->userService->getAuthUserId();
        $this->dishService->updateDishRating($userId, $dishId, $updateDishRatingRequest);

        return new JsonResponse();
    }

    /**
     * Исключить блюдо для пользователя на сегодня
     * @Nelmio\Security(name="Bearer")
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ"
     * )
     * @param int $dishId
     * @return JsonResponse
     */
    public function excludeDishForUserPerDay($dishId): JsonResponse
    {
        if (false === is_numeric($dishId)) {
            throw new SystemException('dishId должен быть числом');
        }
        $dishId = (int) $dishId;

        $userId = $this->userService->getAuthUserId();
        $this->dishService->excludeDishForUserPerDate($userId, $dishId, new DateTimeImmutable('now'));

        return new JsonResponse();
    }

    /**
     * Пользователь выбрал блюдо на сегодня
     * @Nelmio\Security(name="Bearer")
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ"
     * )
     * @param int $dishId
     * @return JsonResponse
     */
    public function setUserDishChoicePerDay($dishId): JsonResponse
    {
        if (false === is_numeric($dishId)) {
            throw new SystemException('dishId должен быть числом');
        }
        $dishId = (int) $dishId;

        $userId = $this->userService->getAuthUserId();

        $this->dishService->setDishChoiceForUserPerDate($userId, $dishId, new DateTimeImmutable('now'));

        return new JsonResponse();
    }
}
