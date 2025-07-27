<?php

namespace App\Enums;


enum TripTimeTypeEnum: string
{
    case INSTANT = 'Instant';
    case SCHEDULED = 'Scheduled';

    public function description(): string
    {
        return match ($this) {
            self::INSTANT => 'Starts immediately',
            self::SCHEDULED => 'Booked for future',
        };
    }
}
