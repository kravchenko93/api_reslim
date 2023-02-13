<?php
declare(strict_types=1);

namespace App\Dto\Dishes;

class DishesWithCategoryResponseDto
{
    /**
     * @var DishCategoryResponseDto
     */
    private $category;

    /**
     * @var DishResponseDto[]
     */
    private $dishes;

    /**
     * @param DishCategoryResponseDto $category
     * @param DishResponseDto[] $dishes
     */
    public function __construct(DishCategoryResponseDto $category, array $dishes)
    {
        $this->category = $category;
        $this->dishes = $dishes;
    }

    /**
     * @return DishCategoryResponseDto
     */
    public function getCategory(): DishCategoryResponseDto
    {
        return $this->category;
    }

    /**
     * @return DishResponseDto[]
     */
    public function getDishes(): array
    {
        return $this->dishes;
    }
}