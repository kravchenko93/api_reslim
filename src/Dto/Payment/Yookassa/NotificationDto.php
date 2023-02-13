<?php
declare(strict_types=1);

namespace App\Dto\Payment\Yookassa;

class NotificationDto
{

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $event;

    /**
     * @var NotificationObjectDto
     */
    private $object;

    /**
     * @param string $type
     * @param string $event
     * @param NotificationObjectDto $object
     */
    public function __construct(
        string $type,
        string $event,
        NotificationObjectDto $object
    ) {
        $this->type = $type;
        $this->event = $event;
        $this->object = $object;
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
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return NotificationObjectDto
     */
    public function getObject(): NotificationObjectDto
    {
        return $this->object;
    }

}