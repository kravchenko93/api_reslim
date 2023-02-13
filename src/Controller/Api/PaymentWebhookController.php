<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\Payment\Yookassa\NotificationDto;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use App\Enum\PaymentEnum;
use Swagger\Annotations as SWG;
use App\Exception\SystemException;

class PaymentWebhookController extends AbstractController
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
     * @return JsonResponse
     *
     * @throws SystemException
     */
    public function webhookYookassa(Request $request): JsonResponse
    {
        $content = $request->getContent();
        /**
         * @var NotificationDto $notification
         */
        $notification = $this->serializer->deserialize($content, NotificationDto::class, 'json');

        $this->paymentService->updatePayment(PaymentEnum::YOOKASSA, $notification->getObject()->getId());

        return new JsonResponse();
    }

    public function testYookassaWait() {
        return new Response('test');
    }

}
