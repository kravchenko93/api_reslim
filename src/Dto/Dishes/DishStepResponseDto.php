<?php
declare(strict_types=1);

namespace App\Dto\Dishes;

class DishStepResponseDto
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $image;

    /**
     * @param string $text
     * @param string $image
     */
    public function __construct(string $text, string $image)
    {
        $this->text = $text;
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getImage(): string {
        return $this->image;
    }
}