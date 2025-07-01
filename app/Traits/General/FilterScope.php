<?php

namespace App\Traits\General;



trait FilterScope
{
    /**
     * Generates a 6-digit two-factor authentication code and sets an expiration time (5 minutes).
     */
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && is_array($value)) {
                $query->where($field, $value);
            }
            // Handle array values (new)
            else {
                $query->whereIn($field, $value);  // WHERE IN (...)
            }
        }

        return $query;
    }
}
