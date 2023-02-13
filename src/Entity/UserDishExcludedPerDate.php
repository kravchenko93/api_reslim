<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserDishExcludedPerDateRepository;
use \DateTime;

/**
 * @ORM\Entity(repositoryClass=UserDishExcludedPerDateRepository::class)
 * @ORM\Table(
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="user_dish_excluded_per_date_unique",
 *            columns={"dish_id", "user_id", "date"})
 *    }
 * )
 */
class UserDishExcludedPerDate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Dish
     *
     * @ORM\ManyToOne(targetEntity="Dish")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $dish;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $date;

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
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return UserDishExcludedPerDate
     */
    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @param Dish $dish
     * @return UserDishExcludedPerDate
     */
    public function setDish(Dish $dish): self
    {
        $this->dish = $dish;

        return $this;
    }

    /**
     * @param User $user
     * @return UserDishExcludedPerDate
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
