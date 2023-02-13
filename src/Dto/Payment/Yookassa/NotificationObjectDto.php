<?php
declare(strict_types=1);

namespace App\Dto\Payment\Yookassa;

class NotificationObjectDto
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @var bool
     */
    private $paid;

    /**
     * @param string $id
     * @param string $status
     * @param bool $paid
     */
    public function __construct(
        string $id,
        string $status,
        bool $paid
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->paid = $paid;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function getPaid(): bool
    {
        return $this->paid;
    }

}