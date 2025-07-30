<?php

namespace App\Models;

use App\Enums\TripStatusEnum;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripStatus extends Model
{
    use FilterScope;
    // =================
    // Configuration
    // =================
    protected $table = 'trip_statuses';
    protected $guarded = ['id'];

    // =================
    // Relationships
    // =================
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    // =================
    // Accessors & Mutators
    // =================
    public function toEnum(): TripStatusEnum
    {
        return TripStatusEnum::from($this->name);
    }
    // =================
    // Scopes
    // =================
    public function scopeActiveStatuses($query)
    {
        return $query->whereIn('name', ['pending', 'in_progress']);
    }

    // =================
    // Business Logic
    // =================
    public function isCompleted(): bool
    {
        return $this->toEnum() === TripStatusEnum::Completed;
    }
    public function isCancelled(): bool
    {
        return str_ends_with($this->name, '_cancelled');
    }
    public function isCancellable(): bool
    {
        return $this->toEnum()->isCancellable();
    }
}
