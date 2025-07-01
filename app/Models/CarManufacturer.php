<?php

namespace App\Models;

use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarManufacturer extends Model
{
    use HasFactory,FilterScope,ActiveScope;

    // =================
    // Configuration
    // =================
    protected $table = 'car_manufacturers';
    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];

    // =================
    // Relationships
    // =================
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function models(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }

    // =================
    // Accessors & Mutators
    // =================


    // =================
    // Scopes
    // =================


    public function scopeByCountry(Builder $query, $countryId): Builder
    {
        return $query->where('country_id', $countryId);
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
