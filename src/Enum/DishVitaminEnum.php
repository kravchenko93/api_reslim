<?php
declare(strict_types=1);

namespace App\Enum;


class DishVitaminEnum
{
    public const A = 'A';
    public const B = 'B';
    public const C = 'C';
    public const D = 'D';
    public const E = 'E';
    public const F = 'F';

    public const ALL = [
        'A' => self::A,
        'B' => self::B,
        'C' => self::C,
        'D' => self::D,
        'E' => self::E,
        'F' => self::F,
    ];
}