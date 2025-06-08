<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;

    // =================
    // Configuration
    // =================
    protected $table = 'cars';
    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];

    // =================
    // Relationships
    // =================
    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function driversCars(): HasMany
    {
        return $this->hasMany(DriverCar::class);
    }
    // =================
    // Accessors & Mutators
    // =================
    public function getFullNameAttribute(): string
    {
        return $this->model->full_name . ' (' . $this->model->model_year . ')';
    }

    public function getSeatInfoAttribute(): string
    {
        return $this->number_of_seats . ' seats';
    }

    // =================
    // Scopes
    // =================
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByModel(Builder $query, $modelId): Builder
    {
        return $query->where('car_model_id', $modelId);
    }

    public function scopeBySeats(Builder $query, $minSeats): Builder
    {
        return $query->where('number_of_seats', '>=', $minSeats);
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

    public function isAvailable(): bool
    {
        return !$this->driverCar || !$this->driverCar->driver->isActive();
    }

    public function getServiceLevel(): CarServiceLevel
    {
        return $this->model->serviceLevel;
    }
}