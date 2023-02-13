<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use DateTime;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ingredient")
 * @Vich\Uploadable
 * @UniqueEntity(
 *  fields="name",
 *  message="Ingredient with same name is exist",
 *  groups={"creation"}
 * )
 */
class Ingredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="public.ingredient_id_seq")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="ingredient_images", fileNameProperty="image")
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
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="DishIngredient", mappedBy="ingredient", cascade={"persist"})
     */
    private $dishIngredients;

    public function __construct() {
        $this->dishIngredients = new ArrayCollection();
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

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return DishIngredient[]|ArrayCollection
     */
    public function getDishIngredients()
    {
        return $this->dishIngredients;
    }
}
