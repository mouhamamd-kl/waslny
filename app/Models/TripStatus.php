<?php

namespace App\Models;

use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

enum TripStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case OnGoing = 'on_going'; // Underscore instead of space
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
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
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucfirst($value), // Example: Format status name
        );
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
    public function isCompletedStatus(): bool
    {
        return $this->name === 'completed';
    }

    public function isCancellable(): bool
    {
        return in_array($this->name, ['pending', 'in_progress']);
    }
}
