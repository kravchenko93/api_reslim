<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserDishChoicePerDateRepository;
use \DateTime;

/**
 * @ORM\Entity(repositoryClass=UserDishChoicePerDateRepository::class)
 * @ORM\Table(
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="user_dish_choice_per_date_unique",
 *            columns={"dish_id", "user_id", "date"})
 *    }
 * )
 */
class UserDishChoicePerDate
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
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $inFact;

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
     * @return UserDishChoicePerDate
     */
    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @param Dish $dish
     * @return UserDishChoicePerDate
     */
    public function setDish(Dish $dish): self
    {
        $this->dish = $dish;

        return $this;
    }

    /**
     * @param User $user
     * @return UserDishChoicePerDate
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

    /**
     * @return bool
     */
    public function getInFact(): bool
    {
        return $this->inFact;
    }

    /**
     * @param bool $inFact
     *
     * @return UserDishChoicePerDate
     */
    public function setInFact(bool $inFact): self
    {
        $this->inFact = $inFact;

        return $this;
    }
}
