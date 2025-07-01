<?php

namespace App\Models;

use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pricing extends Model
{
    use HasFactory, FilterScope, ActiveScope;

    // =================
    // Configuration
    // =================
    protected $table = 'pricings';
    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =================
    // Relationships
    // =================
    public function serviceLevel(): BelongsTo
    {
        return $this->belongsTo(CarServiceLevel::class);
    }

    // =================
    // Accessors & Mutators
    // =================
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price_per_km, 2) . ' per km';
    }

    // =================
    // Scopes
    // =================
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->latest()->limit(1);
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
    public function calculateFare(float $distance): float
    {
        return $distance * $this->price_per_km;
    }

    public function isCurrent(): bool
    {
        // Optional performance optimization
        if ($this->relationLoaded('serviceLevel.currentPricing')) {
            return $this->serviceLevel->currentPricing?->id === $this->id;
        }

        return $this->id === $this->serviceLevel->getCurrentPricing()?->id;
    }
}
