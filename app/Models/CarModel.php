<?php

namespace App\Models;

use App\Traits\Activatable;
use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarModel extends Model
{
    use HasFactory, FilterScope, ActiveScope, Activatable;

    // =================
    // Configuration
    // =================
    protected $table = 'car_models';
    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean',
        'model_year' => 'integer',
    ];

    protected static function booted()
    {
        static::deleting(function (CarModel $carModel) {
            if ($carModel->driversCars()->exists()) {
                throw new \Exception('Cannot delete a car model that has driver cars associated with it.');
            }
        });
    }

    // =================
    // Relationships
    // =================
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(CarManufacturer::class, 'car_manufacturer_id');
    }

    public function serviceLevel(): BelongsTo
    {
        return $this->belongsTo(CarServiceLevel::class, 'car_service_level_id');
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

    public function isRecentModel(): bool
    {
        return $this->model_year >= now()->subYears(2)->year;
    }
}
