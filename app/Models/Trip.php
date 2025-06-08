<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Trip extends Model
{
    /**
     * Configuration
     */
    protected $table = 'trips';
    protected $guarded = ['id']; // Guarded instead of fillable for security
    protected $casts = [];

    /**
     * Relationships
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TripStatus::class, 'trip_status_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TripType::class, 'trip_type_id');
    }

    public function riderCoupon(): BelongsTo
    {
        return $this->belongsTo(RiderCoupon::class)->with('coupon');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(TripLocation::class);
    }



    /**
     * Accessors & Mutators
     */
    // protected function fare(): Attribute
    // {
    //     // return Attribute::make(
    //     //     get: fn($value) => $value / 100, // Convert cents to dollars
    //     //     set: fn($value) => $value * 100, // Convert dollars to cents
    //     // );
    // }

    protected function distanceInKm(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->distance / 1000, // Convert meters to km
        );
    }

    protected function duration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->start_time || !$this->end_time) return null;
                return $this->end_time->diffInMinutes($this->start_time);
            }
        );
    }

    /**
     * Scopes
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
    
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('end_time');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('end_time')->whereNotNull('start_time');
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForRider($query, $riderId)
    {
        return $query->where('rider_id', $riderId);
    }

    public function scopeWithStatus($query, $statusId)
    {
        return $query->where('trip_status_id', $statusId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Business Logic
     */
    public function startTrip(): void
    {
        $this->update([
            'start_time' => now(),
            'trip_status_id' => TripStatus::where('name', 'in_progress')->first()->id
        ]);
    }

    public function completeTrip(): void
    {
        $this->update([
            'end_time' => now(),
            'trip_status_id' => TripStatus::where('name', 'completed')->first()->id
        ]);
    }

    public function applyCoupon(RiderCoupon $riderCoupon): void
    {
        $this->riderCoupon()->associate($riderCoupon);
        $this->recalculateFare();
        $this->save();
    }

    public function recalculateFare(): void
    {
        $baseFare = $this->type->base_fare;
        $distanceCharge = $this->distance * $this->type->price_per_km;

        $discount = $this->riderCoupon?->coupon->discount_amount ?? 0;

        $this->fare = max(0, ($baseFare + $distanceCharge) - $discount);
    }

    public function processPayment(): void
    {
        // $this->rider->wallet->deduct($this->fare);
        // $this->driver->wallet->add($this->fare * 0.8); // 80% to driver

        // Transaction::create([
        //     'wallet_id' => $this->rider->wallet_id,
        //     'amount' => -$this->fare,
        //     'type' => 'trip_charge',
        //     'trip_id' => $this->id
        // ]);

        // Transaction::create([
        //     'wallet_id' => $this->driver->wallet_id,
        //     'amount' => $this->fare * 0.8,
        //     'type' => 'trip_earning',
        //     'trip_id' => $this->id
        // ]);
    }

    public function addLocation(array $locationData): TripLocation
    {
        return $this->locations()->create($locationData);
    }
}
