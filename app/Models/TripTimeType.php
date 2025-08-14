<?php

namespace App\Models;

use App\Traits\Activatable;
use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TripTimeType extends Model
{
    use HasFactory, FilterScope, ActiveScope,Activatable;

    /**
     * Configuration
     */
    protected $table = 'trip_time_types';
    protected $guarded = ['id']; // Guard against mass assignment
    protected $casts = [
        'is_active' => 'boolean',
        'is_system_defined' => 'boolean',
    ];

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
