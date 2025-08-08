<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccountSuspension extends Model
{
    protected $table = 'account_suspensions';
    protected $guarded = ['id'];
    protected $casts = [
        'suspended_until' => 'datetime',
        'lifted_at' => 'datetime',
        'is_permanent' => 'boolean'
    ];

    // Polymorphic relationship
    public function suspendable(): MorphTo
    {
        return $this->morphTo();
    }

    public function suspenssion(): BelongsTo
    {
        return $this->belongsTo(Suspension::class);
    }
    
    // Check if suspension is active
    public function isActive(): bool
    {
        return !$this->isLifted() &&
            ($this->is_permanent ||
                ($this->suspended_until && $this->suspended_until->isFuture()));
    }
    
    public function isLifted(): bool
    {
        return !is_null($this->lifted_at);
    }

    public function lift(): bool
    {
        return $this->update([
            'lifted_at' => now(),
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereRaw('is_permanent = true')
                ->orWhere('suspended_until', '>', now());
        })->whereNull('lifted_at');
    }
}
