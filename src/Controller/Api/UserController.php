<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\CreateUserRequestDto;
use App\Dto\LoginUserRequestDto;
use App\Dto\UpdateUserRequestDto;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Swagger\Annotations as SWG;

class UserController extends AbstractController
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param SerializerInterface $serializer
     * @param UserService $userService
     */
    public function __construct(
        SerializerInterface $serializer,
        UserService $userService
    )
    {
        $this->serializer = $serializer;
        $this->userService = $userService;
    }

    /**
     * Получить данные пользователя
     * @Nelmio\Security(name="Bearer")
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ",
     *   @Nelmio\Model(type=\App\Dto\UserResponseDto::class)
     * )
     * @param int userId
     * @param Request $request
     * @return JsonResponse
     *
     */
    public function getUserInfo($userId, Request $request): JsonResponse
    {
        $userId = $this->userService->getValidatedUserIdFromPath($userId);

        $user = $this->userService->getUser($userId);

        $jsonContent = $this->serializer->serialize($user, 'json');

        return new JsonResponse($jsonContent, 200, [], true);
    }

    /**
     * Обновить пользователя
     * @Nelmio\Security(name="Bearer")
     * @SWG\Parameter(
     *     name="body",
     *     type="string",
     *     in="body",
     *     required=true,
     *     @Nelmio\Model(type=UpdateUserRequestDto::class)
     * )
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ",
     *   @Nelmio\Model(type=\App\Dto\UserResponseDto::class)
     * )
     * @param int userId
     * @param Request $request
     * @return JsonResponse
     */
    public function updateUser($userId, Request $request): JsonResponse
    {
        $content = $request->getContent();
        /**
         * @var UpdateUserRequestDto $updateUserRequest
         */
        $updateUserRequest = $this->serializer->deserialize($content, UpdateUserRequestDto::class, 'json');

        $userId = $this->userService->getValidatedUserIdFromPath($userId);
        $user = $this->userService->updateUser($userId, $updateUserRequest);

        $jsonContent = $this->serializer->serialize($user, 'json');

        return new JsonResponse($jsonContent, 200, [], true);
    }

    /**
     * Создать пользователя
     * @SWG\Parameter(
     *     name="body",
     *     type="string",
     *     in="body",
     *     required=true,
     *     @Nelmio\Model(type=\App\Dto\CreateUserRequestDto::class)
     * )
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ",
     *   @Nelmio\Model(type=\App\Dto\UserResponseDto::class)
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createUser(Request $request): JsonResponse
    {
        $content = $request->getContent();
        /**
         * @var $createUserRequest CreateUserRequestDto
         */
        $createUserRequest = $this->serializer->deserialize($content, CreateUserRequestDto::class, 'json');

        $user = $this->userService->createUser($createUserRequest);

        $jsonContent = $this->serializer->serialize($user, 'json');

        return new JsonResponse($jsonContent, 200, [], true);
    }

    /**
     * Получить токен пользователя
     * @SWG\Parameter(
     *     name="body",
     *     type="string",
     *     in="body",
     *     required=true,
     *     @Nelmio\Model(type=LoginUserRequestDto::class)
     * )
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ",
     *   @Nelmio\Model(type=\App\Dto\TokenResponseDto::class)
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function loginUser(Request $request): JsonResponse
    {
        $content = $request->getContent();
        /**
         * @var LoginUserRequestDto $loginUserRequest
         */
        $loginUserRequest = $this->serializer->deserialize($content, LoginUserRequestDto::class, 'json');
        $token = $this->userService->getToken($loginUserRequest);

        $jsonContent = $this->serializer->serialize($token, 'json');

        return new JsonResponse($jsonContent, 200, [], true);
    }
}
