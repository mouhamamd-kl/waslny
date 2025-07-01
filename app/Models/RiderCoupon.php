<?php

namespace App\Models;

use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RiderCoupon extends Model
{
    use FilterScope;
    // =================
    // Configuration
    // =================

    protected $table = 'rider_coupons';
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // =================
    // Relationships
    // =================

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class, 'rider_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
    
    // =================
    // Scopes
    // =================

    // =================
    // Business Logic
    // =================

    /**
     * Mark coupon as used
     */
    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
        $this->coupon()->increment('used_count');
    }

    /**
     * Check if coupon is expired
     */
    public function isExpired(): bool
    {
        return $this->coupon->end_time->isPast();
    }

    /**
     * Check if coupon is usable
     */
    public function isUsable(): bool
    {
        return !$this->used_at && !$this->isExpired();
    }
}
