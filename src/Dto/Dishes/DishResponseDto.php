<?php
declare(strict_types=1);

namespace App\Dto\Dishes;

class DishResponseDto
{
    /**
     * @var int
     */
    private $id;

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
    private $description;

    /**
     * @var DishIngredientResponseDto[]
     */
    private $ingredients;

    /**
     * @var DishStepResponseDto[]
     */
    private $steps;

    /**
     * @var int|null
     */
    private $rating;

    /**
     * @var string
     */
    private $cookingTools;

    /**
     * @var int
     */
    private $weight;

    /**
     * @var string
     */
    private $cookingTime;

    /**
     * @var string
     */
    private $complexity;

    /**
     * @var int
     */
    private $proteins;

    /**
     * @var int
     */
    private $fats;

    /**
     * @var int
     */
    private $carbohydrates;

    /**
     * @var string[]
     */
    private $vitamins;

    /**
     * @param int $id
     * @param string $name
     * @param string $image
     * @param string $description
     * @param DishIngredientResponseDto[] $ingredients
     * @param DishStepResponseDto[] $steps
     * @param int|null $rating
     * @param string $cookingTools
     * @param int $weight
     * @param string $cookingTime
     * @param string $complexity
     * @param int $proteins
     * @param int $fats
     * @param int $carbohydrates
     * @param string[] $vitamins
     */
    public function __construct(
        int $id,
        string $name,
        string $image,
        string $description,
        array $ingredients,
        array $steps,
        ?int $rating,
        string $cookingTools,
        int $weight,
        string $cookingTime,
        string $complexity,
        int $proteins,
        int $fats,
        int $carbohydrates,
        array $vitamins
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->description = $description;
        $this->ingredients = $ingredients;
        $this->steps = $steps;
        $this->rating = $rating;
        $this->cookingTools = $cookingTools;
        $this->weight = $weight;
        $this->cookingTime = $cookingTime;
        $this->complexity = $complexity;
        $this->proteins = $proteins;
        $this->fats = $fats;
        $this->carbohydrates = $carbohydrates;
        $this->vitamins = $vitamins;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return DishIngredientResponseDto[]
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /**
     * @return DishStepResponseDto[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @return string[]
     */
    public function getVitamins(): array
    {
        return $this->vitamins;
    }

    /**
     * @return string
     */
    public function getComplexity(): string
    {
        return $this->complexity;
    }

    /**
     * @return int
     */
    public function getProteins(): int
    {
        return $this->proteins;
    }

    /**
     * @return int
     */
    public function getFats(): int
    {
        return $this->fats;
    }

    /**
     * @return int
     */
    public function getCarbohydrates(): int
    {
        return $this->carbohydrates;
    }

    /**
     * @return string
     */
    public function getCookingTools(): string
    {
        return $this->cookingTools;
    }

    /**
     * @return string
     */
    public function getCookingTime(): string
    {
        return $this->cookingTime;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }
}