<?php

namespace App\Models;

use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Clickbar\Magellan\Data\Geometries\Point;


class RiderSavedLocation extends Model
{
    use HasFactory, FilterScope;

    // =================
    // Configuration
    // =================
    /**
     * The table associated with the model.
     */
    protected $table = 'rider_saved_locations';

    /**
     * Guarded attributes against mass assignment.
     */
    protected $guarded = ['id'];


    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'location' => Point::class,
    ];

    // =================
    // Relationships
    // =================

    /**
     * Rider who owns this location
     */
    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    /**
     * Folder containing this location
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(RiderFolder::class, 'folder_id');
    }

    // =================
    // Accessors & Mutators
    // =================

    /**
     * Get location as array [lat, lng]
     */

    /**
     * Set location from array or Point object
     */

    // public function setLocationAttribute($value): void
    // {
    //     $this->attributes['location'] = $value instanceof Point
    //         ? $value
    //         : Point::make($value[0], $value[1]); // [lat, lng]
    // }

    // =================
    // Scopes
    // =================

    /**
     * Scope for locations within a distance (meters)
     */
    public function scopeWithinDistance($query, Point $center, int $radius)
    {
        return $query->whereDistanceSphere('location', $center, '<', $radius);
    }

    /**
     * Scope for locations in a specific folder
     */
    public function scopeInFolder($query, $folderId)
    {
        return $query->where('folder_id', $folderId);
    }

    // =================
    // Business Logic
    // =================

    /**
     * Convert to GeoJSON feature
     */
    // public function toGeoJsonFeature(): array
    // {
    //     return [
    //         'type' => 'Feature',
    //         'properties' => $this->only(['id', 'name', 'folder_id']),
    //         'geometry' => $this->location->toArray()
    //     ];
    // }

    /**
     * Check if location belongs to a rider
     */
    public function belongsToRider(int $riderId): bool
    {
        return $this->rider_id === $riderId;
    }
}
