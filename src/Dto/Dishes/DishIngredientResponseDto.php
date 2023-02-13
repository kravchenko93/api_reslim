<?php
declare(strict_types=1);

namespace App\Dto\Dishes;


class DishIngredientResponseDto
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @param string $name
     * @param string $image
     * @param string $quantity
     */
    public function __construct(string $name, string $image, string $quantity)
    {
        $this->name = $name;
        $this->image = $image;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImage(): string {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getQuantity(): string {
        return $this->quantity;
    }
}