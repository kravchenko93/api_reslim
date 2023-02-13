<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\YookassaPaymentRepository;

/**
 * @ORM\Entity(repositoryClass=YookassaPaymentRepository::class)
 * @ORM\Table(name="yookassa_payment")
 */
class YookassaPayment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="public.yookassa_payment_id_seq")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $yookassaId;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    /**
     * @var int
     * @ORM\Column(type="float", nullable=false)
     */
    private $amount;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $paid;

    /**
     * токен для автоплатежа
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $paymentMethodId;

    /**
     * @var UserSubscription
     *
     * @ORM\OneToOne(targetEntity="UserSubscription", inversedBy="yookassaPayment")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $userSubscription;

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return UserSubscription
     */
    public function getUserSubscription(): UserSubscription
    {
        return $this->userSubscription;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getYookassaId(): string
    {
        return $this->yookassaId;
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * @param  bool $paid
     *
     * @return YookassaPayment
     */
    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * @param  string $status
     *
     * @return YookassaPayment
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param int $amount
     *
     * @return YookassaPayment
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param string $yookassaId
     *
     * @return YookassaPayment
     */
    public function setYookassaId(string $yookassaId): self
    {
        $this->yookassaId = $yookassaId;

        return $this;
    }

    /**
     * @param string|null $paymentMethodId
     *
     * @return YookassaPayment
     */
    public function setPaymentMethodId(?string $paymentMethodId): self
    {
        $this->paymentMethodId = $paymentMethodId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentMethodId(): ?string
    {
        return $this->paymentMethodId;
    }

    /**
     * @param UserSubscription $userSubscription
     * @return YookassaPayment
     */
    public function setUserSubscription(UserSubscription $userSubscription): self
    {
        $this->userSubscription = $userSubscription;

        return $this;
    }
}
