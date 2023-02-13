<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;

class CreateUserRequestDto
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email
     */
    private $email;

    /**
     * @var array
     * @Assert\NotBlank()
     * @SWG\Property(type="array", @SWG\Items(type="string"))
     */
    private $info;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $email
     * @param array $info
     * @param string $password
     */
    public function __construct(?string $email, ?array $info, ?string $password)
    {
        $this->email = $email;
        $this->info = $info;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return array
     */
    public function getInfo() {
        return $this->info;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }
}