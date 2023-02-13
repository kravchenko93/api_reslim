<?php
declare(strict_types=1);

namespace App\Dto;

class TokenResponseDto
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $userId;

    /**
     * @param string $token
     * @param int $userId
     */
    public function __construct(string $token, int $userId)
    {
        $this->token = $token;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getUserId(): int {
        return $this->userId;
    }
}