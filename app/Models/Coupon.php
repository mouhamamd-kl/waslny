<?php

namespace App\Models;

use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, FilterScope;

    // =================
    // Configuration
    // =================

    protected $table = 'coupons';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $casts = [];
    // Add this line to explicitly set the factory class
    // =================
    // Relationships
    // =================

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'cupon_id'); // Note: 'cupon_id' matches schema
    }

    public function riderCoupons(): HasMany
    {
        return $this->hasMany(RiderCoupon::class, 'coupon_id');
    }

    public function riders(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Rider::class, 'rider_coupons')
            ->using(RiderCoupon::class)
            ->withPivot('used_at')
            ->withTimestamps();
    }

    // =================
    // Accessors & Mutators
    // =================

    /**
     * Get formatted discount amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->isPercentage()
            ? "{$this->amount}%"
            : config('app.currency_symbol') . number_format($this->amount, 2);
    }

    /**
     * Set code to uppercase
     */
    public function setCodeAttribute($value): void
    {
        $this->attributes['code'] = strtoupper($value);
    }

    // =================
    // Scopes
    // =================

    public function scopeActive(Builder $query): Builder
    {
        $now = now()->toDateString();
        return $query->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->whereColumn('used_count', '<', 'max_uses');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('end_date', '<', now())
            ->orWhereColumn('used_count', '>=', 'max_uses');
    }

    public function scopeForRider(Builder $query, Rider $rider): Builder
    {
        return $query->whereHas('riders', fn($q) => $q->where('id', $rider->id));
    }

    // =================
    // Business Logic
    // =================

    /**
     * Apply coupon to a trip fare
     */
    public function applyDiscount(float $fare): float
    {
        return $this->isPercentage()
            ? $fare * (1 - $this->amount / 100)
            : max(0, $fare - $this->amount);
    }

    /**
     * Check if coupon is percentage-based
     */
    public function isPercentage(): bool
    {
        return $this->type === 'percentage';
    }

    /**
     * Check if coupon is expired
     */
    public function isExpired(): bool
    {
        return $this->end_date < now() || $this->used_count >= $this->max_uses;
    }

    /**
     * Check if coupon is active
     */
    public function isActive(): bool
    {
        return !$this->isExpired() && $this->start_date <= now();
    }

    /**
     * Increment usage count
     */
    public function recordUsage(): void
    {
        $this->increment('used_count');
    }

    // /**
    //  * Assign coupon to a rider
    //  */
    // public function assignToRider(Rider $rider): RiderCoupon
    // {
    //     return $this->riders()->attach($rider, [
    //         'used_at' => Carbon::now()
    //     ]);
    // }

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
}
