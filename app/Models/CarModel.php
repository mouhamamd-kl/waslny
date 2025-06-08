<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarModel extends Model
{
    use HasFactory;

    // =================
    // Configuration
    // =================
    protected $table = 'car_models';
    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean',
        'model_year' => 'integer',
    ];

    // =================
    // Relationships
    // =================
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(CarManufacturer::class);
    }

    public function serviceLevel(): BelongsTo
    {
        return $this->belongsTo(CarServiceLevel::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    // =================
    // Accessors & Mutators
    // =================

    public function FullNameAttribute(): string
    {
        return $this->manufacturer->name . ' ' . $this->name;
    }

    // =================
    // Scopes
    // =================
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByManufacturer(Builder $query, $manufacturerId): Builder
    {
        return $query->where('car_manufacturer_id', $manufacturerId);
    }

    public function scopeByServiceLevel(Builder $query, $serviceLevelId): Builder
    {
        return $query->where('car_service_level_id', $serviceLevelId);
    }

    // =================
    // Business Logic
    // =================
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function isRecentModel(): bool
    {
        return $this->model_year >= now()->subYears(2)->year;
    }
}
