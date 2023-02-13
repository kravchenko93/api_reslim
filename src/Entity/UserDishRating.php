<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserDishRatingRepository;

/**
 * @ORM\Entity(repositoryClass=UserDishRatingRepository::class)
 * @UniqueEntity(
 *  fields={"dish", "user"},
 *  message="Duplicate record",
 *  groups={"creation"}
 * )
 */
class UserDishRating
{
    /**
     * @var Dish
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Dish")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $dish;

    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 5
     * )
     */
    private $rating;

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Dish
     */
    public function getDish(): Dish
    {
        return $this->dish;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     * @return UserDishRating
     */
    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @param Dish $dish
     * @return UserDishRating
     */
    public function setDish(Dish $dish): self
    {
        $this->dish = $dish;

        return $this;
    }

    /**
     * @param User $user
     * @return UserDishRating
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
