<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RiderCoupon extends Model
{
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
