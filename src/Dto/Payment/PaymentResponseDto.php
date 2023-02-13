<?php
declare(strict_types=1);

namespace App\Dto\Payment;


class PaymentResponseDto
{

    /**
     * @var string
     */
    private $returnUrl;

    /**
     * PaymentResponseDto constructor.
     * @param string $returnUrl
     */
    public function __construct(string $returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }
}