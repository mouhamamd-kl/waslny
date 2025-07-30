<?php

namespace App\Traits\General;
use Illuminate\Support\Str;


trait FilterScope
{
    /**
     * Generates a 6-digit two-factor authentication code and sets an expiration time (5 minutes).
     */
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && is_array($value)) {
                $query->whereIn($field, $value);
            }
            if (Str::startsWith($field, 'min_')) {
                $query->where(substr($field, 4), '>=', $value);
                continue;
            } elseif (Str::startsWith($field, 'max_')) {
                $query->where(substr($field, 4), '<=', $value);
                continue;
            }
            // Handle array values (new)
            else {
                $query->where($field, $value);  // WHERE IN (...)
            }
        }
        return $query;
    }
}
