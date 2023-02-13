<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use DateTime;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\DishRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DishRepository::class)
 * @ORM\Table(name="dish")
 * @Vich\Uploadable
 * @UniqueEntity(
 *  fields="name",
 *  message="Dish with same name is exist",
 *  groups={"creation"}
 * )
 */
class Dish
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="public.dish_id_seq")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="dish_images", fileNameProperty="image")
     * @var File|null
     * @Assert\File(
     *     maxSize = "2048k",
     *     mimeTypes = {
     *           "image/png",
     *          "image/jpeg",
     *          "image/jpg",
     *          "image/gif"
     *     },
     *     mimeTypesMessage = "Please upload a valid image"
     * )
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DishCategory")
     * @ORM\JoinColumn(name="dish_category_id", referencedColumnName="id")
     */
    private $dishCategory;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     * @var boolean
     */
    private $hide;

    /**
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="DishIngredient", mappedBy="dish", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $dishIngredients;

    /**
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="DishStep", mappedBy="dish", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $dishSteps;

    /**
     * @ORM\OneToMany(targetEntity="UserDishRating", mappedBy="dish", fetch="EXTRA_LAZY")
     */
    private $userDishRatings;

    /**
     * @ORM\OneToMany(targetEntity="UserDishExcludedPerDate", mappedBy="dish", fetch="EXTRA_LAZY")
     */
    private $userDishExcludedPerDate;

    /**
     * @ORM\OneToMany(targetEntity="UserDishChoicePerDate", mappedBy="dish", fetch="EXTRA_LAZY")
     */
    private $userDishChoicePerDate;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $cookingTools;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $weight;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $cookingTime;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $complexity;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $proteins;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $fats;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $carbohydrates;

    /**
     * @ORM\Column(type="json_array", nullable=false)
     */
    private $vitamins = [];

    public function __construct()
    {
        $this->dishIngredients = new ArrayCollection();
        $this->userDishRatings = new ArrayCollection();
        $this->userDishExcludedPerDate = new ArrayCollection();
        $this->userDishChoicePerDate = new ArrayCollection();
        $this->dishSteps = new ArrayCollection();
    }

    /**
     * @param File|null $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;
    }

    /**
     * @return File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @param string $image
     */
    public function setImage(?string $image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return DishCategory
     */
    public function getDishCategory(): ?DishCategory
    {
        return $this->dishCategory;
    }

    /**
     * @param DishCategory $dishCategory
     * @return Dish
     */
    public function setDishCategory(DishCategory $dishCategory): self
    {
        $this->dishCategory = $dishCategory;

        return $this;
    }

    /**
     * @return DishIngredient[]|ArrayCollection
     */
    public function getDishIngredients()
    {
        return $this->dishIngredients;
    }

    /**
     * @return DishStep[]|ArrayCollection
     */
    public function getDishSteps()
    {
        return $this->dishSteps;
    }

    /**
     * @return UserDishRating[]|ArrayCollection
     */
    public function getUserDishRatings()
    {
        return $this->userDishRatings;
    }

    /**
     * @return UserDishExcludedPerDate[]|ArrayCollection
     */
    public function getUserDishExcludedPerDate()
    {
        return $this->userDishExcludedPerDate;
    }

    /**
     * @return UserDishChoicePerDate[]|ArrayCollection
     */
    public function getUserDishChoicePerDate()
    {
        return $this->userDishChoicePerDate;
    }

    /**
     * @param DishIngredient[] $dishIngredients
     *
     * @return Dish
     */
    public function setDishIngredients($dishIngredients): self
    {
        $this->dishIngredients = $dishIngredients;

        return $this;
    }

    /**
     * @param DishStep[] $dishSteps
     *
     * @return Dish
     */
    public function setDishSteps($dishSteps): self
    {
        $this->dishSteps = $dishSteps;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return bool
     */
    public function getHide(): bool
    {
        return $this->hide;
    }

    /**
     * @param bool $hide
     *
     * @return Dish
     */
    public function setHide(bool $hide)
    {
        $this->hide = $hide;

        return $this;
    }

    /**
     * @param int $weight
     *
     * @return Dish
     */
    public function setWeight(int $weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @return string
     */
    public function getCookingTime(): string
    {
        return $this->cookingTime;
    }

    /**
     * @param string $cookingTime
     *
     * @return Dish
     */
    public function setCookingTime(string $cookingTime)
    {
        $this->cookingTime = $cookingTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getCookingTools(): string
    {
        return $this->cookingTools;
    }

    /**
     * @param string $cookingTools
     *
     * @return Dish
     */
    public function setCookingTools(string $cookingTools)
    {
        $this->cookingTools = $cookingTools;

        return $this;
    }

    /**
     * @return int
     */
    public function getCarbohydrates(): int
    {
        return $this->carbohydrates;
    }

    /**
     * @param int $carbohydrates
     *
     * @return Dish
     */
    public function setCarbohydrates(int $carbohydrates)
    {
        $this->carbohydrates = $carbohydrates;

        return $this;
    }

    /**
     * @return int
     */
    public function getFats(): int
    {
        return $this->fats;
    }

    /**
     * @param int $fats
     *
     * @return Dish
     */
    public function setFats(int $fats)
    {
        $this->fats = $fats;

        return $this;
    }

    /**
     * @return int
     */
    public function getProteins(): int
    {
        return $this->proteins;
    }

    /**
     * @param int $proteins
     *
     * @return Dish
     */
    public function setProteins(int $proteins)
    {
        $this->proteins = $proteins;

        return $this;
    }

    /**
     * @return string
     */
    public function getComplexity(): string
    {
        return $this->complexity;
    }

    /**
     * @param string $complexity
     *
     * @return Dish
     */
    public function setComplexity(string $complexity)
    {
        $this->complexity = $complexity;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getVitamins(): array
    {
        $vitamins = $this->vitamins ?? [];

        return array_unique($vitamins);
    }

    /**
     * @param array|string[] $vitamins
     * @return Dish
     */
    public function setVitamins(array $vitamins): self
    {
        $this->vitamins = $vitamins;

        return $this;
    }
}
