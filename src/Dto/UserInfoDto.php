<?php
declare(strict_types=1);

namespace App\Dto;

class UserInfoDto
{
    public function __toString(): string
    {
        return !empty((array) $this) ? json_encode((array) $this) : '{}';
    }
}