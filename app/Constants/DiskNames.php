<?php

namespace App\Constants;

trait EnumFromName
{
    public static function valueFromName(string $name): self
    {
        return constant("self::$name");
    }
}
use Exception;

// class DiskNames
// {
//     public const RIDERS = 'riders_profile';
//     public const DRIVERS_PROFILE = 'drivers_profile';
//     public const DRIVERS_LICENSE = 'drivers_license';
//     public const DRIVERS_CAR_PHOTOS = 'drivers_car_photos';
//     public const SYSTEM = 'system';
//     public const PUBLIC = 'subabase';
//     public const PRIVATE = 'supabase_private';  // Don't forget this one!
// }

enum DiskNames: string
{
    use EnumFromName;
    case RIDERS = 'riders/profile';
    case DRIVERS_PROFILE = 'drivers/profile';
    case DRIVERS_LICENSE = 'drivers/license';
    case DRIVERS_CAR_PHOTOS = 'drivers/car-photos';
    case SYSTEM = 'system';
    case SUBAPASEPUBLIC = 'subabase';
    case SUPABASEPRIVATE = 'supabase_private';
    // Get all valid disk names (keys)
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    // Get all valid disk paths (values)
    public static function paths(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Validate if name exists
    public static function isValidName(string $name): void
    {
        $result = in_array($name, self::names(), true);
        if ($result == false) {
            throw new Exception('the disk name' . $name . 'is not valid');
        }
    }
}
