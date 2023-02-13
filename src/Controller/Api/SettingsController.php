<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\JsonSchemaService;
use App\Enum\JsonSchemaNameEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Swagger\Annotations as SWG;
use App\Dto\SettingsResponseDto;
use Symfony\Component\Serializer\SerializerInterface;

class SettingsController extends AbstractController
{
    /**
     * @var JsonSchemaService $jsonSchemaService
     */
    private $jsonSchemaService;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param JsonSchemaService $jsonSchemaService
     * @param SerializerInterface $serializer
     */
    public function __construct(
        JsonSchemaService $jsonSchemaService,
        SerializerInterface $serializer
    )
    {
        $this->jsonSchemaService = $jsonSchemaService;
        $this->serializer = $serializer;
    }

    /**
     * Получить настройки
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ",
     *   @Nelmio\Model(type=\App\Dto\SettingsResponseDto::class)
     * )
     * @return JsonResponse
     *
     */
    public function getSettings(): JsonResponse
    {
        $userQuestionsFormSchema = $this->jsonSchemaService->getSchema(JsonSchemaNameEnum::USER_QUESTIONS_FORM);

        $settings = new SettingsResponseDto(
            $userQuestionsFormSchema
        );

        $jsonContent = $this->serializer->serialize($settings, 'json');

        return new JsonResponse($jsonContent, 200, [], true);
    }
}
