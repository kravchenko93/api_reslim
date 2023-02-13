<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;

class UpdateUserRequestDto
{
    /**
     * @var array
     * @Assert\NotBlank()
     * @Assert\Type("array")
     * @SWG\Property(type="array", @SWG\Items(type="string"))
     */
    private $info;

    /**
     * @param array $info
     */
    public function __construct(?array $info)
    {
        $this->info = $info;
    }

    /**
     * @return array
     */
    public function getInfo() {
        return $this->info;
    }
}