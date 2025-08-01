<?php

namespace App\Models;

use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarServiceLevel extends Model
{
    use HasFactory, FilterScope, ActiveScope;

    // =================
    // Configuration
    // =================
    protected $table = 'car_service_levels';
    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];

    // =================
    // Relationships
    // =================
    public function carModels(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }

    public function pricings(): HasMany
    {
        return $this->hasMany(Pricing::class);
    }

    // =================
    // Accessors & Mutators
    // =================


    // =================
    // Scopes
    // =================


    public function scopeWithCurrentPricing(Builder $query): Builder
    {
        return $query->with(['pricings' => function ($query) {
            $query->active()->latest('created_at');
        }]);
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

    public function getCurrentPricing(): ?Pricing
    {
        return $this->pricings()->active()->latest()->first();
    }
}
