<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class RiderFolder extends Model
{
    // =================
    // Configuration
    // =================
    protected $table = 'rider_folders';
    protected $guarded = ['id'];

    // =================
    // Relationships
    // =================
    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function savedLocations(): HasMany
    {
        return $this->hasMany(RiderSavedLocation::class);
    }

    // =================
    // Scopes
    // =================
    public function scopeForRider($query, $riderId)
    {
        return $query->where('rider_id', $riderId);
    }

    // =================
    // Business Logic
    // =================
    public function locationsCount(): int
    {
        return $this->savedLocations()->count();
    }
}
