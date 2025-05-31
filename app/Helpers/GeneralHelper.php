<?php

use App\Constants\DiskNames;

if (!function_exists('getConstantNameByValue')) {
    /**
     * Generate public URL for Supabase Storage file
     *
     * @param string $value
     * @param string $class
     * @return string
     */

    function getConstantNameByValue(string $value, string $class): ?string
    {
        $reflection = new \ReflectionClass($class);
        $constants = $reflection->getConstants();

        foreach ($constants as $name => $val) {
            if ($val === $value) {
                return $name;
            }
        }
        return null; // Value not found
    }
}
