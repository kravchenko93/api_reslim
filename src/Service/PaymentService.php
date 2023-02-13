<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\Payment\{PaymentRequestDto, PaymentResponseDto};
use App\Entity\{UserSubscription, YookassaPayment};
use App\Enum\PaymentEnum;
use App\Exception\SystemException;
use App\Exception\ValidationException;
use App\Repository\UserSubscriptionRepository;
use App\Repository\UserRepository;
use App\Repository\YookassaPaymentRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use YooKassa\Client as YooKassaClient;

class PaymentService
{
    const AMOUNT = 2;
    const YOOKASSA_PAYMENT_STATUS_SUCCEEDED = 'succeeded';
    const PAYMENT_TYPE_TRIAL = 'trial';
    const PAYMENT_TYPE_FULL = 'full';
    const PAYMENT_TRIAL_PERIOD = '+1 week';
    const PAYMENT_FULL_PERIOD = '+1 year';

    /**
     * @var ValidatorService
     */
    private $validatorService;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var YookassaPaymentRepository
     */
    private $yookassaPaymentRepository;

    /**
     * @var UserSubscriptionRepository
     */
    private $userSubscriptionRepository;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param ValidatorService $validatorService
     * @param ParameterBagInterface $params
     * @param YookassaPaymentRepository $yookassaPaymentRepository
     * @param UserSubscriptionRepository $userSubscriptionRepository
     * @param UserService $userService
     * @param UserRepository $userRepository
     */
    public function __construct(
        ValidatorService $validatorService,
        ParameterBagInterface $params,
        YookassaPaymentRepository $yookassaPaymentRepository,
        UserSubscriptionRepository $userSubscriptionRepository,
        UserService $userService,
        UserRepository $userRepository
    )
    {
        $this->validatorService = $validatorService;
        $this->params = $params;
        $this->yookassaPaymentRepository = $yookassaPaymentRepository;
        $this->userSubscriptionRepository = $userSubscriptionRepository;
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $typePayment
     * @param PaymentRequestDto $paymentRequest
     * @return PaymentResponseDto
     *
     * @throws SystemException
     * @throws ValidationException
     */
    public function createPayment(string $typePayment, PaymentRequestDto $paymentRequest): PaymentResponseDto
    {
        $this->validatorService->validateDto($paymentRequest);

        switch ($typePayment) {
            case PaymentEnum::YOOKASSA:
                return $this->createPaymentYookassa($paymentRequest);

            default:
                throw new SystemException('Type payment not found');
        }

    }

    /**
     * @param PaymentRequestDto $paymentRequest
     * @return PaymentResponseDto
     */
    private function createPaymentYookassa(PaymentRequestDto $paymentRequest): PaymentResponseDto
    {
        $yooKassaClient = $this->getYooKassaClient();
        $createPaymentResponse = $yooKassaClient->createPayment(
            [
                'amount' => [
                    'value' => self::AMOUNT,
                    'currency' => 'RUB',
                ],
                'payment_method_data' => [
                    'type' => 'bank_card',
                    'card' => [
                        'cardholder' => $paymentRequest->getCardholder(),
                        'csc' => $paymentRequest->getCsc(),
                        'expiry_month' => $paymentRequest->getExpiryMonth(),
                        'expiry_year' => $paymentRequest->getExpiryYear(),
                        'number' => $paymentRequest->getNumber(),
                    ],
                ],
                //обычный режим оплаты (в одну стадию)
                'capture' => true,
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => $this->params->get('yoo_kassa.return_url'),
                ],
                'save_payment_method' => true,
            ],
            uniqid('', true)
        );

        $userId = $this->userService->getAuthUserId();
        $user = $this->userRepository->find($userId);

        $userSubscription = new UserSubscription();
        $userSubscription->setPaymentType(PaymentEnum::YOOKASSA);
        $userSubscription->setType(self::PAYMENT_TYPE_TRIAL);
        $userSubscription->setDateStart(new \DateTime('now'));
        $userSubscription->setDateFinish(new \DateTime(self::PAYMENT_TRIAL_PERIOD));
        $userSubscription->setPaid(false);
        $userSubscription->setUser($user);

        $em = $this->userSubscriptionRepository->getEm();
        $em->persist($userSubscription);
        $em->flush($userSubscription);

        $yookassaPayment = new YookassaPayment();
        $yookassaPayment->setPaid(false);
        $yookassaPayment->setAmount(self::AMOUNT);
        $yookassaPayment->setStatus($createPaymentResponse->getStatus());
        $yookassaPayment->setYookassaId($createPaymentResponse->getId());
        $yookassaPayment->setUserSubscription($userSubscription);

        $em = $this->yookassaPaymentRepository->getEm();
        $em->persist($yookassaPayment);
        $em->flush($yookassaPayment);

        return new PaymentResponseDto($createPaymentResponse->getConfirmation()->getConfirmationUrl());
    }

    /**
     * @param string $typePayment
     * @param string $paymentId
     * @return void
     *
     * @throws SystemException
     */
    public function updatePayment(string $typePayment, string $paymentId): void
    {

        switch ($typePayment) {
            case PaymentEnum::YOOKASSA:
                $this->updatePaymentYookassa($paymentId);
                return;

            default:
                throw new SystemException('Type payment not found');
        }

    }

    /**
     * @param string $paymentId
     * @return void
     *
     * @throws SystemException
     */
    private function updatePaymentYookassa(string $paymentId): void
    {
        $yooKassaClient = $this->getYooKassaClient();
        $paymentResponse = $yooKassaClient->getPaymentInfo($paymentId);

        if (
            null !== $paymentResponse &&
            true === $paymentResponse->getPaid() &&
            self::YOOKASSA_PAYMENT_STATUS_SUCCEEDED === $paymentResponse->getStatus()
        ) {
            $yookassaPayment = $this->yookassaPaymentRepository->findOneBy(['yookassaId' => $paymentId]);
            if (null === $yookassaPayment) {
                throw new SystemException('not have saved yookassa payment with id ' . $paymentId);
            }

            $userSubscription = $yookassaPayment->getUserSubscription();
            $userSubscription->setPaid(true);

            $em = $this->userSubscriptionRepository->getEm();
            $em->persist($userSubscription);
            $em->flush($userSubscription);

            $yookassaPayment->setPaid(true);
            if (true === $paymentResponse->getPaymentMethod()->getSaved()) {
                //токен для автоплатежа
                $yookassaPayment->setPaymentMethodId($paymentResponse->getPaymentMethod()->getId());
            }
            $yookassaPayment->setStatus($paymentResponse->getStatus());

            $em = $this->yookassaPaymentRepository->getEm();
            $em->persist($yookassaPayment);
            $em->flush($yookassaPayment);

        }
    }

    /**
     * @param int $userId
     *
     * @return void
     *
     * @throws SystemException
     */
    public function autoPay(int $userId): void
    {
        $user = $this->userRepository->find($userId);

        $userSubscription = $this->userSubscriptionRepository->findOneBy(['user' => $user], ['dateFinish' => 'DESC']);

        if (null === $userSubscription || null === $userSubscription->getYookassaPayment()->getPaymentMethodId()) {
            return;
        }

        $paymentMethodId = $userSubscription->getYookassaPayment()->getPaymentMethodId();

        $yooKassaClient = $this->getYooKassaClient();
        $createPaymentResponse = $yooKassaClient->createPayment(
            array(
                'amount' => array(
                    'value' => self::AMOUNT,
                    'currency' => 'RUB',
                ),
                'payment_method_id' => $paymentMethodId
            ),
            uniqid('', true)
        );


        if (self::YOOKASSA_PAYMENT_STATUS_SUCCEEDED === $createPaymentResponse->getStatus()) {
            $userSubscription = new UserSubscription();
            $userSubscription->setPaymentType(PaymentEnum::YOOKASSA);
            $userSubscription->setType(self::PAYMENT_TYPE_FULL);
            $userSubscription->setDateStart(new \DateTime('now'));
            $userSubscription->setDateFinish(new \DateTime(self::PAYMENT_FULL_PERIOD));
            $userSubscription->setPaid(true);
            $userSubscription->setUser($user);

            $em = $this->userSubscriptionRepository->getEm();
            $em->persist($userSubscription);
            $em->flush($userSubscription);

            $yookassaPayment = new YookassaPayment();
            $yookassaPayment->setPaid(true);
            $yookassaPayment->setAmount(self::AMOUNT);
            $yookassaPayment->setStatus($createPaymentResponse->getStatus());
            $yookassaPayment->setYookassaId($createPaymentResponse->getId());
            $yookassaPayment->setUserSubscription($userSubscription);

            if (true === $createPaymentResponse->getPaymentMethod()->getSaved()) {
                //токен для автоплатежа
                $yookassaPayment->setPaymentMethodId($createPaymentResponse->getPaymentMethod()->getId());
            }

            $em = $this->yookassaPaymentRepository->getEm();
            $em->persist($yookassaPayment);
            $em->flush($yookassaPayment);
        } else {
            //обработка кейса, когда автоплатеж не сработал
        }


    }

    private function getYooKassaClient(): YooKassaClient
    {
        $client = new YooKassaClient();
        $client->setAuth($this->params->get('yoo_kassa.id_market'), $this->params->get('yoo_kassa.secret_key'));

        return $client;
    }
}