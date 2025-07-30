<?php

namespace App\Models;

use App\Enums\TripStatusEnum;
use App\Exceptions\InvalidTransitionException;
use App\Traits\General\FilterScope;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

class Trip extends Model
{
    use FilterScope;
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

    public function notifications(): HasMany
    {
        return $this->hasMany(TripDriverNotification::class);
    }

    public function notifiedDrivers(): HasManyThrough
    {
        return $this->hasManyThrough(
            Driver::class,
            TripDriverNotification::class,
            'trip_id',
            'id',
            'id',
            'driver_id'
        );
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
    public function timeType(): BelongsTo
    {
        return $this->belongsTo(TripTimeType::class, 'trip_time_type_id');
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
    public function durationInMinutes(): ?int
    {
        // Check if both times are set
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        // Calculate duration in minutes
        return $this->end_time->diffInMinutes($this->start_time);
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

    public function transitionTo(TripStatusEnum $newStatus): void
    {
        $currentStatus = TripStatusEnum::tryFrom($this->status->name);

        if (!$currentStatus->canTransitionTo($newStatus)) {
            throw new InvalidTransitionException(
                "Cannot transition from {$currentStatus->value} to {$newStatus->value}"
            );
        }

        // Get database status model
        $newStatusModel = TripStatus::firstWhere('name', $newStatus->value);
        if (!$newStatusModel) {
            throw new \RuntimeException("Status {$newStatus->value} not found in database");
        }
        // Update status timeline
        $timeline = $this->status_timeline ?? [];
        $timeline[$newStatus->value] = now()->toDateTimeString();

        // Update trip status and timeline
        $this->trip_status_id = $newStatusModel->id;
        $this->status_timeline = $timeline;
        $this->save();

        // Fire events
        // event(new TripStatusChanged($this, $currentStatus, $newStatus));
    }
    // Accessor for any status time

    public function isNotified(Driver $driver): bool
    {
        return $this->notifications()
            ->where('driver_id', $driver->id)
            ->exists();
    }

    public function assignDriver(Driver $driver): void
    {
        if ($this->driver_id) {
            throw new Exception('Trip already has a driver assigned');
        }

        $this->driver()->associate($driver);
        $this->transitionTo(TripStatusEnum::DriverAssigned);
        $this->save();


        // Update driver status
        $driver->update([
            'driver_status_id' => DriverStatus::where('name', 'in_trip')->first()->id
        ]);
    }

    public function recordNotification(Driver $driver): void
    {
        $this->notifications()->firstOrCreate([
            'driver_id' => $driver->id,
            'sent_at' => now()
        ]);
    }

    public function getStatusTime(TripStatusEnum $status): ?Carbon
    {
        return isset($this->status_timeline[$status->value])
            ? Carbon::parse($this->status_timeline[$status->value])
            : null;
    }

    // Aliases for common status times
    public function getStartedAtAttribute(): ?Carbon
    {
        return $this->getStatusTime(TripStatusEnum::OnGoing);
    }

    public function getCancelledAtAttribute(): ?Carbon
    {
        return $this->getStatusTime(TripStatusEnum::RiderCancelled) ??
            $this->getStatusTime(TripStatusEnum::DriverCancelled) ??
            $this->getStatusTime(TripStatusEnum::SystemCancelled);
    }

    public function getCompletedAtAttribute(): ?Carbon
    {
        return $this->getStatusTime(TripStatusEnum::Completed);
    }

    public function startTrip(): void
    {
        $this->transitionTo(TripStatusEnum::OnGoing);
        $this->start_time = now();  // Still track specific timing if needed
        $this->save();
    }

    public function completeTrip(): void
    {
        $this->transitionTo(TripStatusEnum::Completed);
        $this->end_time = now();
        $this->save();
    }

    public function cancelByRider(): void
    {
        $this->transitionTo(TripStatusEnum::RiderCancelled);
        $this->save();
    }
    public function cancelByDriver(): void
    {
        $this->transitionTo(TripStatusEnum::DriverCancelled);
        $this->save();
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

    public function addLocation(array $locations): TripLocation
    {
        return $this->locations()->create($locations);
    }
}
