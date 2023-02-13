<?php
declare(strict_types=1);

namespace App\Dto;

class SubscriptionInfoDto
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $fields;

    /**
     * @param string $type
     * @param array $fields
     */
    public function __construct(string $type, array $fields)
    {
      $this->type = $type;
      $this->fields = $fields;
    }

//    public function __toString(): string
//    {
//        return !empty((array) $this) ? json_encode((array) $this) : '{}';
//    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}