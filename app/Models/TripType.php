<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TripType extends Model
{
    use HasFactory;

    /**
     * Configuration
     */
    protected $table = 'trip_types';
    protected $guarded = ['id']; // Guard against mass assignment

    // Default trip type constants
    public const STANDARD = 'standard';
    public const PREMIUM = 'premium';
    public const SUV = 'SUV';

    /**
     * Relationships
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'trip_type_id');
    }

    /**
     * Accessors & Mutators
     */
    protected function displayName(): Attribute
    {
        return $this->name;
    }

    /**
     * Scopes
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

    public function scopeStandard(Builder $query): Builder
    {
        return $query->where('name', self::STANDARD);
    }

    public function scopePremium(Builder $query): Builder
    {
        return $query->where('name', self::PREMIUM);
    }

    public function scopeExecutive(Builder $query): Builder
    {
        return $query->where('name', self::SUV);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount('trips')
            ->orderByDesc('trips_count');
    }

    /**
     * Business Logic
     */
    public function isStandard(): bool
    {
        return $this->name === self::STANDARD;
    }

    public function isPremium(): bool
    {
        return $this->name === self::PREMIUM;
    }

    public function isSUV(): bool
    {
        return $this->name === self::SUV;
    }
}
