<?php
declare(strict_types=1);

namespace App\Dto;

use Swagger\Annotations as SWG;

class UserResponseDto
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @SWG\Property(type="array", @SWG\Items(type="string"))
     * @var array
     */
    private $info;

    /**
     * @var UserSubscriptionResponseDto
     */
    private $subscription;

    /**
     * @param int $id
     * @param string $email
     * @param array $info
     * @param UserSubscriptionResponseDto|null $subscription
     */
    public function __construct(int $id, string $email, array $info, ?UserSubscriptionResponseDto $subscription)
    {
        $this->id = $id;
        $this->email = $email;
        $this->info = $info;
        $this->subscription = $subscription;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @return UserSubscriptionResponseDto|null
     */
    public function getSubscription(): ?UserSubscriptionResponseDto
    {
        return $this->subscription;
    }
}