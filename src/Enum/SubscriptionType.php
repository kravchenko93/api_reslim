<?php
declare(strict_types=1);

namespace App\Enum;

class SubscriptionType
{
    public const TRIAL = 'trial';
    public const FIRST = 'first';
    public const SECOND = 'second';
    public const THIRD = 'third';

    public const ALL = [
        self::TRIAL,
        self::FIRST,
        self::SECOND,
        self::THIRD,
    ];

}