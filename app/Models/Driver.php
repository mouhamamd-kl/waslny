<?php

namespace App\Models;

use App\Services\FileServiceFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

enum DriverPhotoType: string
{
    case PROFILE = 'profile';
    case DRIVERLICENSE = 'driver_license';
}

class Driver extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_OFFLINE = 'offline';
    const STATUS_AVAILABLE = 'available';
    const STATUS_ON_TRIP = 'ontrip';

    // =================
    // Configuration
    // =================

    protected $table = 'drivers';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'suspended' => 'boolean',
        'avg_rating' => 'float',
    ];

    // =================
    // Relationships
    // =================
    public function status(): BelongsTo
    {
        return $this->belongsTo(DriverStatus::class, 'status_id');
    }

    public function driverCar(): HasOne
    {
        return $this->hasOne(DriverCar::class, 'driver_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'driver_id');
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

    // =================
    // Accessors & Mutators
    // =================
    public function getProfilePhotoAttribute(): ?string
    {
        if (!$this->profile_photo) {
            return null;
        }
        return filter_var($this->profile_photo, FILTER_VALIDATE_URL)
            ? $this->profile_photo
            : FileServiceFactory::makeForDriverProfile()->getUrl($this->profile_photo);
    }

    public function getDriverLicensePhotoAttribute(): ?string
    {
        if (!$this->driving_licence) {
            return null;
        }

        return filter_var($this->driving_licence, FILTER_VALIDATE_URL)
            ? $this->driving_licence
            : FileServiceFactory::makeForDriverLicense()->getUrl($this->driving_licence);
    }


    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // public function setPhoneNumberAttribute($value): void
    // {
    //     $this->attributes['phone_number'] = preg_replace('/[^0-9+]/', '', $value);
    // }

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

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas(
            'status',
            fn($q) =>
            $q->where('name', self::STATUS_AVAILABLE)
        );
    }

    public function scopeOnTrip(Builder $query): Builder
    {
        return $query->whereHas(
            'status',
            fn($q) =>
            $q->where('name', self::STATUS_ON_TRIP)
        );
    }

    public function scopeOffline(Builder $query): Builder
    {
        return $query->whereHas(
            'status',
            fn($q) =>
            $q->where('name', self::STATUS_OFFLINE)
        );
    }

    // =================
    // Status Helpers
    // =================

    public function isAvailable(): bool
    {
        return $this->status->name === self::STATUS_AVAILABLE;
    }

    public function isOnTrip(): bool
    {
        return $this->status->name === self::STATUS_ON_TRIP;
    }

    public function isOffline(): bool
    {
        return $this->status->name === self::STATUS_OFFLINE;
    }

    // =================
    // Business Logic
    // =================
    public function deletePhoto(DriverPhotoType $type): bool
    {
        $column = $type->value . '_photo';
        $path = $this->getRawOriginal($column);

        if (!$path) return true;

        try {
            $service = FileServiceFactory::makeForDriverCarPhotos();
            $service->delete($path);
            $this->attributes[$column] = null;
            return $this->save();
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function deletePhotos(): bool
    {
        $success = true;
        $service = FileServiceFactory::makeForDriverCarPhotos();

        foreach (DriverPhotoType::cases() as $type) {
            $column = $type->value . '_photo';
            $path = $this->getRawOriginal($column);

            if (empty($path)) {
                continue; // Skip if no photo exists
            }

            try {
                // Delete the file from storage
                $service->delete($path);
                // Clear the attribute without saving yet (batch update)
                $this->attributes[$column] = null;
            } catch (Exception $e) {
                report($e); // Log the error for debugging
                $success = false;
                // Continue trying to delete other photos even if one fails
            }
        }

        // Save once after all deletions are attempted
        return $success && $this->save();
    }

    public function suspend(): void
    {
        $this->update(['suspended' => true]);
        $this->setStatus(self::STATUS_OFFLINE);
    }

    public function reinstate(): void
    {
        $this->update(['suspended' => false]);
        $this->setStatus(self::STATUS_OFFLINE);
    }

    public function setStatus(string $status): void
    {
        $statusId = DriverStatus::firstWhere('status', $status)?->id;
        if ($statusId) {
            $this->update(['status_id' => $statusId]);
        }
    }

    public function recalculateRating(): void
    {
        $average = $this->completedTrips()
            ->whereNotNull('driver_rating')
            ->avg('driver_rating');

        $this->update(['avg_rating' => $average ?? $this->avg_rating]);
    }
}
