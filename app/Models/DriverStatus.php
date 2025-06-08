<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverStatus extends Model
{
    // =================
    // Configuration
    // =================
    protected $table = 'driver_statuses';
    protected $guarded = ['id']; // or use `protected $fillable = ['name'];`

    // =================
    // Relationships
    // =================
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    // =================
    // Scopes
    // =================
    // (Add scopes here if needed, e.g., `scopeActive()`)
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
