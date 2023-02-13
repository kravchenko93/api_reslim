<?php
declare(strict_types=1);

namespace App\Dto\Dishes;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateDishRatingRequestDto
{
    /**
     * @var int
     * @Assert\Range(
     *      min = 1,
     *      max = 5
     * )
     */
    private $rating;

    /**
     * @param int $rating
     */
    public function __construct(?int $rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return int
     */
    public function getRating() {
        return $this->rating;
    }
}