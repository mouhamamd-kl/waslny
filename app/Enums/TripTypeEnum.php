<?php

namespace App\Enums;

enum TripTypeEnum: string
{
    case REGULAR = 'Regular';
    case DIFFERENT_LOCATIONS = 'Different Locations';
    case ROUND_TRIP = 'Round Trip';
    // Add more types as needed

    public function description(): string
    {
        return match ($this) {
            self::REGULAR => 'Standard point-to-point trip from pickup location to destination',
            self::DIFFERENT_LOCATIONS => 'Multiple stops at different locations before final destination',
            self::ROUND_TRIP => 'Return to original pickup location after reaching destination',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
