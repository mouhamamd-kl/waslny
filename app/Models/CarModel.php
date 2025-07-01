<?php

namespace App\Models;

use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarModel extends Model
{
    use HasFactory, FilterScope, ActiveScope;

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

    public function driversCars(): HasMany
    {
        return $this->hasMany(DriverCar::class);
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
    public function isActive(): bool
    {
        return $this->is_active;
    }
    
    public function isRecentModel(): bool
    {
        return $this->model_year >= now()->subYears(2)->year;
    }
}
