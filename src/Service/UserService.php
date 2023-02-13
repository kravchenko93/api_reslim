<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\UserSubscription;
use App\Exception\SystemException;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Dto\UpdateUserRequestDto;
use App\Dto\CreateUserRequestDto;
use App\Dto\TokenResponseDto;
use App\Dto\{UserResponseDto, UserSubscriptionResponseDto};
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Enum\UserRoleEnum;
use App\Enum\JsonSchemaNameEnum;
use App\Dto\LoginUserRequestDto;

class UserService
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var JsonSchemaService $jsonSchemaService
     */
    private $jsonSchemaService;

    /**
     * @var ValidatorService
     */
    private $validatorService;

    /**
     * @param UserRepository $userRepository
     * @param TokenStorageInterface $tokenStorage
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param ParameterBagInterface $params
     * @param JsonSchemaService $jsonSchemaService
     * @param ValidatorService $validatorService
     */
    public function __construct(
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ParameterBagInterface $params,
        JsonSchemaService $jsonSchemaService,
        ValidatorService $validatorService

    ) {
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->params = $params;
        $this->jsonSchemaService = $jsonSchemaService;
        $this->validatorService = $validatorService;
    }

    /**
     * @param int $userId
     * @return UserResponseDto|null
     */
    public function getUser(int $userId): ?UserResponseDto {
        $userModel = $this->userRepository->find($userId);

        if (null === $userModel) {
            return null;
        }

        return $this->getUserDtoByModel($userModel);
    }

    /**
     * @param string $email
     * @return UserResponseDto|null
     */
    public function getUserByEmail(string $email): ?UserResponseDto {
        $userModel =  $this->userRepository->findOneBy([
            'email' => $email
        ]);

        if (null === $userModel) {
            return null;
        }

        return $this->getUserDtoByModel($userModel);
    }

    /**
     * @return UserResponseDto[]
     */
    public function getUsers(): array {

        return array_map(function (User $userModel) {
            return $this->getUserDtoByModel($userModel);
        },  $this->userRepository->findAll());
    }

    /**
     * @param int $userId
     * @param UpdateUserRequestDto $updateUserRequest
     * @return UserResponseDto
     */
    public function updateUser(int $userId, UpdateUserRequestDto $updateUserRequest): UserResponseDto
    {
        $this->validatorService->validateDto($updateUserRequest);
        $this->jsonSchemaService->validateData(JsonSchemaNameEnum::USER_QUESTIONS_FORM, $updateUserRequest->getInfo());

        $user = $this->userRepository->find($userId);
        $user->setInfo($updateUserRequest->getInfo());

        $em = $this->userRepository->getEm();
        $em->persist($user);
        $em->flush($user);

        return $this->getUserDtoByModel($user);
    }

    /**
     * @param CreateUserRequestDto $createUserRequest
     * @return UserResponseDto
     * @throws SystemException
     */
    public function createUser(CreateUserRequestDto $createUserRequest): UserResponseDto
    {
        $this->validatorService->validateDto($createUserRequest);

        $user = $this->getUserByEmail($createUserRequest->getEmail());

        if (null !== $user) {
            throw new SystemException('email is used');
        }

        $this->jsonSchemaService->validateData(JsonSchemaNameEnum::USER_QUESTIONS_FORM, $createUserRequest->getInfo());

        $user = new User();
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $createUserRequest->getPassword()));
        $user->setEmail($createUserRequest->getEmail());
        $user->setRoles([UserRoleEnum::ROLE_USER]);
        $user->setInfo($createUserRequest->getInfo());
        $em = $this->userRepository->getEm();
        $em->persist($user);
        $em->flush($user);

        return $this->getUserDtoByModel($user);
    }

    /**
     * @param $userId
     * @return int
     * @throws SystemException
     */
    public function getValidatedUserIdFromPath($userId) {
        if (false === is_numeric($userId)) {
            throw new SystemException('userId должен быть числом');
        }

        $userId = (int) $userId;

        $token = $this->tokenStorage->getToken();
        /**
         * @var User $authUser
         */
        $authUser = $token->getUser();
        if ($authUser->getId() !== $userId) {
            throw new SystemException('нет прав на просмотр данных пользователя с таким userId');
        }

        return $userId;
    }

    /**
     * @param string $password
     * @param string $email
     * @return boolean
     */
    public function checkValidPasswordByEmail(string $password, string $email): bool
    {
        $user = $this->userRepository->findUserByEmail(
            $email
        );

        if (null === $user || !$this->userPasswordEncoder->isPasswordValid($user, $password)) {
            return false;
        }

        return true;
    }

    /**
     * @param LoginUserRequestDto $loginUserRequest
     * @return TokenResponseDto
     * @throws SystemException
     */
    public function getToken(LoginUserRequestDto $loginUserRequest): TokenResponseDto {
        $this->validatorService->validateDto($loginUserRequest);

        if (false === $this->checkValidPasswordByEmail($loginUserRequest->getPassword(), $loginUserRequest->getEmail())) {
            throw new SystemException('email or password is wrong.');
        }

        $user = $this->userRepository->findUserByEmail($loginUserRequest->getEmail());

        $payload = [
            "user" => $user->getUsername(),
            "exp" => (new \DateTime())->modify("+105 minutes")->getTimestamp(),
        ];

        $jwt = JWT::encode($payload, $this->params->get('jwt_secret'), 'HS256');

        return new TokenResponseDto(
            sprintf('Bearer %s', $jwt),
            $user->getId()
        );
    }

    /**
     * @param User $user
     * @return UserResponseDto
     */
    private function getUserDtoByModel (User $user): UserResponseDto
    {
        return new UserResponseDto(
            $user->getId(),
            $user->getEmail(),
            $user->getInfo(),
            $this->getUserSubscriptionDtoByModel($user->getActiveUserSubscription())
        );
    }

    /**
     * @param UserSubscription|null $userSubscription
     * @return UserSubscriptionResponseDto|null
     */
    private function getUserSubscriptionDtoByModel (?UserSubscription $userSubscription): ?UserSubscriptionResponseDto
    {
        if (null === $userSubscription) {
            return null;
        }

        return new UserSubscriptionResponseDto(
            $userSubscription->getDateFinish(),
            $userSubscription->getType(),
            $userSubscription->getPaymentType()
        );
    }

    /**
     * @return int|null
     */
    public function getAuthUserId(): ?int
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return null;
        }
        /**
         * @var User $authUser
         */
        $authUser = $token->getUser();

        return $authUser->getId();
    }
}