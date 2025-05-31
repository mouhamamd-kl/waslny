<?php

namespace App\Constants;

use InvalidArgumentException;

class FileExtensions
{
    // Common image formats
    public const IMAGES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Document formats
    public const DOCUMENTS = ['pdf', 'doc', 'docx', 'txt'];

    // Specific use cases (aligned with your disks)
    public const PROFILE_PHOTOS = ['jpg', 'jpeg', 'png'];
    public const LICENSE_FILES = ['jpg', 'jpeg', 'png', 'pdf'];
    public const CAR_PHOTOS = ['jpg', 'jpeg', 'png'];
    public const SYSTEM_FILES = ['jpg', 'jpeg', 'png', 'pdf', 'docx', 'xlsx'];

    // Generic categories
    public const ALL = ['*']; // Use with caution!
    public const NONE = [];   // For special cases

    /**
     * Validate that the input array exactly matches one of the constants
     */
    public static function isValidExtension(array $input): void
    {
        $reflection = new \ReflectionClass(self::class);
        $constants = $reflection->getConstants();
        $normalizedInput = self::normalizeArray($input);

        foreach ($constants as $name => $value) {
            if (self::normalizeArray($value) === $normalizedInput) {
                return;
            }
        }

        throw new InvalidArgumentException(sprintf(
            "Invalid extension array. Must match one of: %s",
            implode(', ', array_keys($constants))
        ));
    }

    /**
     * Normalize array for comparison (sorted, unique values)
     */
    private static function normalizeArray(array $arr): string
    {
        $filtered = array_filter($arr, fn($v) => $v !== '*');
        $unique = array_unique($filtered);
        sort($unique);
        return implode(',', $unique);
    }

    /**
     * Get all valid constant arrays
     */
    public static function getAllConstants(): array
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getConstants();
    }
}
