<?php

namespace App\Models;

use App\Services\FileServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Rider extends Model
{
    use HasFactory;

    // =================
    // Configuration
    // =================

    protected $table = 'riders';
    protected $primaryKey = 'id'; // Explicitly define since it's BIGINT

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'suspended' => 'boolean',
        'avg_rating' => 'float',
    ];

    // =================
    // Relationships
    // =================

    /**
     * Default payment method for the rider
     */
    public function defaultPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'defaul_payment_id');
    }

    /**
     * Saved locations for the rider
     */
    public function savedLocations(): HasMany
    {
        return $this->hasMany(RiderSavedLocation::class, 'rider_id');
    }

    /**
     * Folders created by the rider
     */
    public function folders(): HasMany
    {
        return $this->hasMany(RiderFolder::class, 'rider_id');
    }

    /**
     * Trips taken by the rider
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'rider_id');
    }

    public function completedTrips(): HasMany
    {
        return $this->trips()
            ->whereHas('status', fn($q) => $q->where('name', 'completed'));
    }

    public function currentTrip(): HasOne
    {
        return $this->hasOne(Trip::class, 'driver_id')
            ->whereHas('status', fn($q) => $q->where('name', 'in_progress'));
    }
    /**
     * Coupons owned by the rider (through pivot)
     */
    public function coupons()
    {
        return $this->hasMany(RiderCoupon::class)->withTimestamps();
    }

    // =================
    // Accessors & Mutators
    // =================

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function setPhoneNumberAttribute($value): void
    {
        $this->attributes['phone_number'] = preg_replace('/[^0-9+]/', '', $value);
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo) {
            return null;
        }

        return filter_var($this->profile_photo, FILTER_VALIDATE_URL)
            ? $this->profile_photo
            : FileServiceFactory::makeForRiderProfile()->getUrl($this->profile_photo);
    }



    // =================
    // Scopes
    // =================

    /**
     * Filter riders by parameters
     */
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && is_array($value)) {
                $query->where($field, $value);
            }
            // Handle array values (new)
            else {
                $query->whereIn($field, $value);  // WHERE IN (...)
            }
        }

        return $query;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('suspended', false);
    }

    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('suspended', true);
    }

    // =================
    // Business Logic
    // =================

    public function suspend(): void
    {
        $this->update(['suspended' => true]);
    }

    public function reinstate(): void
    {
        $this->update(['suspended' => false]);
    }

    /**
     * Update rider rating based on completed trips
     */
    public function recalculateRating(): void
    {
        $average = $this->trips()
            ->whereNotNull('rating')
            ->avg('rating');

        $this->update(['avg_rating' => $average ?? $this->avg_rating]);
    }

    /**
     * Check if rider has payment method set
     */
    public function hasPaymentMethod(): bool
    {
        return !is_null($this->defaul_payment_id);
    }
}
