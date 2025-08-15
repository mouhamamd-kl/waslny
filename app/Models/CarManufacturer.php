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

class CarManufacturer extends Model
{
    use HasFactory, FilterScope, ActiveScope,Activatable;

    // =================
    // Configuration
    // =================
    protected $table = 'car_manufacturers';
    protected $guarded = ['id'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function booted()
    {
        static::deleting(function (CarManufacturer $carManufacturer) {
            if ($carManufacturer->models()->exists()) {
                throw new \Exception('Cannot delete a car manufacturer that has models associated with it.');
            }
        });
    }

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
}
