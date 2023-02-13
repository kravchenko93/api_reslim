<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\Payment\{PaymentRequestDto, PaymentResponseDto};
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use App\Enum\PaymentEnum;
use Swagger\Annotations as SWG;
use App\Exception\SystemException;
use App\Exception\ValidationException;

class PaymentController extends AbstractController
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * @param SerializerInterface $serializer
     * @param PaymentService $paymentService
     */
    public function __construct(
        SerializerInterface $serializer,
        PaymentService $paymentService
    )
    {
        $this->serializer = $serializer;
        $this->paymentService = $paymentService;
    }

    /**
     * Создать платеж для Yookassa
     * @Nelmio\Security(name="Bearer")
     * @SWG\Parameter(
     *     name="body",
     *     type="string",
     *     in="body",
     *     required=true,
     *     @Nelmio\Model(type=PaymentRequestDto::class)
     * )
     * @SWG\Response(
     *   response="200",
     *   description="Успешный ответ",
     *   @Nelmio\Model(type=PaymentResponseDto::class)
     * )
     * @return JsonResponse
     *
     * @throws SystemException
     * @throws ValidationException
     */
    public function createYookassaPayment(Request $request): JsonResponse
    {
        $content = $request->getContent();
        /**
         * @var PaymentRequestDto $paymentRequest
         */
        $paymentRequest = $this->serializer->deserialize($content, PaymentRequestDto::class, 'json');

        $paymentResponse = $this->paymentService->createPayment(PaymentEnum::YOOKASSA, $paymentRequest);

        $jsonContent = $this->serializer->serialize($paymentResponse, 'json');

        return new JsonResponse($jsonContent, 200, [], true);
    }
}
