<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    // =================
    // Configuration
    // =================
    protected $table = 'countries';
    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];

    // =================
    // Relationships
    // =================
    public function manufacturers(): HasMany
    {
        return $this->hasMany(CarManufacturer::class);
    }

    // =================
    // Accessors & Mutators
    // =================

    
    // =================
    // Scopes
    // =================
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount('manufacturers')
            ->orderByDesc('manufacturers_count');
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
}