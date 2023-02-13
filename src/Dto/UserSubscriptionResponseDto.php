<?php
declare(strict_types=1);

namespace App\Dto;

use DateTime;

class UserSubscriptionResponseDto
{

    /**
     * @var DateTime
     */
    private $finish;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $paymentType;

    /**
     * @param DateTime $finish
     * @param string $type
     * @param string $paymentType
     */
    public function __construct(DateTime $finish, string $type, string $paymentType)
    {
        $this->finish = $finish;
        $this->type = $type;
        $this->paymentType = $paymentType;
    }

    /**
     * @return DateTime
     */
    public function getFinish(): DateTime
    {
        return $this->finish;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPaymentType(): string
    {
        return $this->paymentType;
    }
}