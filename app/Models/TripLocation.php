<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripLocation extends Model
{

    /**
     * Configuration
     */
    protected $table = 'trip_locations';
    protected $guarded = ['id'];
    protected $casts = [
        'location' => Point::class,
        'estimated_arrival_time' => 'datetime',
        'actual_arrival_time' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Accessors & Mutators
     */
    protected function getLang()
    {
        return  $this->location->getLongitude(); // Instance of Point
    }
    protected function getLat()
    {
        return  $this->location->getLatitude(); // Instance of Point

    }

    protected function arrivalDelay()
    {
        return $this->actual_arrival_time->diffInMinutes($this->estimated_arrival_time);
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
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopePickupPoints($query)
    {
        return $query->where('location_type', 'pickup');
    }

    public function scopeDropoffPoints($query)
    {
        return $query->where('location_type', 'dropoff');
    }

    public function scopeStops($query)
    {
        return $query->where('location_type', 'stop');
    }

    /**
     * Business Logic
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'actual_arrival_time' => now(),
            'is_completed' => true
        ]);
    }

    public function updateEstimatedArrival($minutes): void
    {
        $this->update([
            'estimated_arrival_time' => now()->addMinutes($minutes)
        ]);
    }

    public function distanceTo(TripLocation $location): float
    {
        if (!$this->location || !$location->location) {
            return 0.0;
        }

        return $this->location->distanceTo($location->location);
    }

    public function isPickup(): bool
    {
        return $this->location_type === 'pickup';
    }

    public function isDropoff(): bool
    {
        return $this->location_type === 'dropoff';
    }

    public function isStop(): bool
    {
        return $this->location_type === 'stop';
    }
}
