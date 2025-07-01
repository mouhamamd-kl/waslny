<?php

namespace App\Enums;

enum LocationTypeEnum: string
{
    case Pickup = 'pickup';
    case Stop = 'stop';
    case DropOff = 'dropoff'; // Underscore instead of space

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    public static function rule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}