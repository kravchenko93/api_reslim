<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\DishIngredientRepository;

/**
 * @ORM\Entity(repositoryClass=DishIngredientRepository::class)
 * @ORM\Table()
 */
class DishIngredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Dish")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dish;

    /**
     * @ORM\ManyToOne(targetEntity="Ingredient")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $ingredient;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sort;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $quantity;

    /**
     * @param int $sort
     *
     * @return self
     */
    public function setSort(int $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @param Dish $dish
     *
     * @return self
     */
    public function setDish(Dish $dish): self
    {
        $this->dish = $dish;
        return $this;
    }

    /**
     * @param Ingredient $ingredient
     *
     * @return self
     */
    public function setIngredient(Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;
        return $this;
    }

    /**
     * @return Ingredient
     */
    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    /**
     * @return Dish
     */
    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    /**
     * @return int
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     *
     * @return self
     */
    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }


    public function __toString(): string
    {
        if (null === $this->getIngredient()) return '';
        return $this->getIngredient()->getName() . ' - ' . $this->getQuantity();
    }
}
