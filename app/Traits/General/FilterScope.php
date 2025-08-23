<?php

namespace App\Traits\General;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;


trait FilterScope
{
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $this->applyFilters($query, $filters);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            // Skip null values to avoid empty where clauses.
            if ($value === null) {
                continue;
            }

            if (Str::contains($field, '.') && $value == true) {
                [$relation, $relationField] = explode('.', $field, 2);
                if (strtolower(class_basename($query->getModel())) === strtolower($relation)) {
                    $query->$relationField();
                    continue;
                }
                $query->whereHas($relation, function (Builder $q) use ($relationField, $value) {
                    $q->$relationField();
                });
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
