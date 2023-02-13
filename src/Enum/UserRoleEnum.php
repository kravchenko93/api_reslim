<?php
declare(strict_types=1);

namespace App\Enum;


class UserRoleEnum
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ALL = [
        'Admin' => self::ROLE_ADMIN,
        'User' => self::ROLE_USER
    ];
}