<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TripTimeType extends Model
{
    use HasFactory;

    /**
     * Configuration
     */
    protected $table = 'trip_types';
    protected $guarded = ['id']; // Guard against mass assignment

    // Default trip type constants
    public const Instant = 'instant';
    public const Scheduled = 'scheduled';

    /**
     * Relationships
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'trip_time_type_id');
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

    public function scopeInstant(Builder $query): Builder
    {
        return $query->where('name', self::Instant);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('name', self::Scheduled);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount('trips')
            ->orderByDesc('trips_count');
    }

    /**
     * Business Logic
     */
    public function isScheduled(): bool
    {
        return $this->name === self::Scheduled;
    }

    public function isInstant(): bool
    {
        return in_array($this->name, [self::Instant,]);
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
