<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserSubscriptionRepository;

/**
 * @ORM\Entity(repositoryClass=UserSubscriptionRepository::class)
 * @ORM\Table(name="user_subscription")
 */
class UserSubscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="public.user_subscription_id_seq")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $paymentType;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateStart;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateFinish;

    /**
     * @var YookassaPayment|null
     *
     * @ORM\OneToOne(targetEntity="YookassaPayment", mappedBy="userSubscription")
     */
    private $yookassaPayment;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $paid;

    /**
     * @return DateTime
     */
    public function getDateStart(): DateTime
    {
        return $this->dateStart;
    }

    /**
     * @return DateTime
     */
    public function getDateFinish(): DateTime
    {
        return $this->dateFinish;
    }

    /**
     * @return YookassaPayment|null
     */
    public function getYookassaPayment(): ?YookassaPayment
    {
        return $this->yookassaPayment;
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
    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param  DateTime $dateStart
     *
     * @return UserSubscription
     */
    public function setDateStart(DateTime $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * @param  DateTime $dateFinish
     *
     * @return UserSubscription
     */
    public function setDateFinish(DateTime $dateFinish): self
    {
        $this->dateFinish = $dateFinish;

        return $this;
    }

    /**
     * @param  string $type
     *
     * @return UserSubscription
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param  string $paymentType
     *
     * @return UserSubscription
     */
    public function setPaymentType(string $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserSubscription
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
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
     * @return UserSubscription
     */
    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function __toString(): string
    {
        return '#' . $this->getType() . ' ' . $this->getDateFinish()->format('d.m.Y');
    }
}
