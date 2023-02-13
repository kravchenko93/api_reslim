<?php
declare(strict_types=1);

namespace App\Dto\Payment;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequestDto
{
    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $cardholder;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $csc;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $expiryMonth;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $expiryYear;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $number;

    public function __construct(
        string $cardholder,
        string $csc,
        string $expiryMonth,
        string $expiryYear,
        string $number
    ) {
       $this->cardholder =  $cardholder;
        $this->csc =  $csc;
        $this->expiryMonth =  $expiryMonth;
        $this->expiryYear =  $expiryYear;
        $this->number =  $number;
    }

    /**
     * @return string
     */
    public function getCardholder(): string
    {
        return $this->cardholder;
    }

    /**
     * @return string
     */
    public function getCsc(): string
    {
        return $this->csc;
    }

    /**
     * @return string
     */
    public function getExpiryMonth(): string
    {
        return $this->expiryMonth;
    }

    /**
     * @return string
     */
    public function getExpiryYear(): string
    {
        return $this->expiryYear;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }
}