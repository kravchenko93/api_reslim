<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\DishStepRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use function Webmozart\Assert\Tests\StaticAnalysis\string;

/**
 * @ORM\Entity(repositoryClass=DishStepRepository::class)
 * @Vich\Uploadable
 */
class DishStep
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
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sort;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="dish_step_images", fileNameProperty="image")
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
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
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
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }


    public function __toString(): string
    {
        return 'Шаг ' . $this->getSort();
    }

    /**
     * @param File|null $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param string $image
     */
    public function setImage(?string $image)
    {
        $this->image = $image;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }
}
