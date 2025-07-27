<?php

namespace App\Enums;

enum DriverStatusEnum: string
{
    case STATUS_OFFLINE = 'offline';
    case STATUS_AVAILABLE = 'available';
    case STATUS_ON_TRIP = 'ontrip';
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
