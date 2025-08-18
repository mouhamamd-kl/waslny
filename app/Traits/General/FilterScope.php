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
            // Skip null values to avoid empty where clauses.
            if ($value === null) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
                continue;
            }

            if (Str::startsWith($field, 'min_')) {
                $query->where(substr($field, 4), '>=', $value);
                continue;
            }

            if (Str::startsWith($field, 'max_')) {
                $query->where(substr($field, 4), '<=', $value);
                continue;
            }

            if ($field === 'start_date') {
                // Use whereDate to compare the date part of the column.
                $query->whereDate('start_date', '>=', $value);
                continue;
            }

            if ($field === 'end_date') {
                $query->whereDate('end_date', '<=', $value);
                continue;
            }

            if ($field === 'code' || $field === 'name') {
                $query->where($field, 'like', '%' . $value . '%');
                continue;
            }

            // Default case for simple equality.
            $query->where($field, $value);
        }

        return $query;
    }
}
