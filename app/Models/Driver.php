<?php

namespace App\Models;

use App\Enums\DriverStatusEnum;
use App\Enums\SuspensionReason;
use App\Services\BaseFileService;
use App\Services\FileServiceFactory;
use App\Traits\General\FilterScope;
use App\Traits\General\ResetOTP;
use App\Traits\General\Suspendable;
use App\Traits\General\TwoFactorCode;
use App\Traits\General\TwoFactorCodeGenerator;
use App\Traits\HasDeviceToken;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallet;
use Clickbar\Magellan\Data\Geometries\Point;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

enum DriverPhotoType: string
{
    case PROFILE = 'profile';
    case DRIVERLICENSE = 'driver_license';
    public function serviceMethod(): BaseFileService
    {
        return match ($this) {
            self::PROFILE => FileServiceFactory::makeForDriverProfile(),
            self::DRIVERLICENSE => FileServiceFactory::makeForDriverLicense(),
        };
    }
}

class Driver extends Authenticatable implements Wallet
{
    use HasFactory, Notifiable, HasApiTokens, TwoFactorCode, FilterScope, ResetOTP, Suspendable, HasWallet, HasDeviceToken, SoftDeletes;
    use Suspendable {
        suspendTemporarily as protected traitSuspendTemp;
        suspendForever as protected traitSuspendForever;

        reinstate as protected traitReinstate;
    }

    // Status constants


    // =================
    // Configuration
    // =================

    protected $table = 'drivers';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $casts = [
        'location' => Point::class,
        'birth_date' => 'date',
        'two_factor_expires_at' => 'datetime',
        'avg_rating' => 'float',
    ];

    protected static function booted()
    {
        static::deleting(function (Driver $driver) {
            if ($driver->isForceDeleting()) {
                $driver->driverCar()->delete();
                $driver->tripNotifications()->delete();
                $driver->suspensions()->delete();
            }
        });
    }

    // =================
    // Relationships
    // =================
    public function status(): BelongsTo
    {
        return $this->belongsTo(DriverStatus::class, 'driver_status_id');
    }

    public function driverCar(): HasOne
    {
        return $this->hasOne(DriverCar::class, 'driver_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    public function tripNotifications(): HasMany
    {
        return $this->hasMany(TripDriverNotification::class);
    }

    public function notifiedTrips(): HasManyThrough
    {
        return $this->hasManyThrough(
            Trip::class,
            TripDriverNotification::class,
            'driver_id',
            'id',
            'id',
            'trip_id'
        );
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
    public function suspensions()
    {
        return $this->morphMany(AccountSuspension::class, 'suspendable');
    }
    // =================
    // Accessors & Mutators
    // =================

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


    public function scopeActive(Builder $query): Builder
    {
        return $query->where('suspended', false);
    }

    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('suspended', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas(
            'status',
            fn($q) =>
            $q->where('name', DriverStatusEnum::STATUS_AVAILABLE)
        );
    }

    public function scopeOnTrip(Builder $query): Builder
    {
        return $query->whereHas(
            'status',
            fn($q) =>
            $q->where('name', DriverStatusEnum::STATUS_ON_TRIP)
        );
    }

    public function scopeOffline(Builder $query): Builder
    {
        return $query->whereHas(
            'status',
            fn($q) =>
            $q->where('name', DriverStatusEnum::STATUS_OFFLINE)
        );
    }

    public function scopeNotNotifiedForTrip(Builder $query, Trip $trip): Builder
    {
        return $query->whereDoesntHave('tripNotifications', function ($q) use ($trip) {
            $q->where('trip_id', $trip->id);
        });
    }

    // =================
    // Status Helpers
    // =================

    public function isAvailable(): bool
    {
        return $this->status->name === DriverStatusEnum::STATUS_AVAILABLE;
    }

    public function isOnTrip(): bool
    {
        return $this->status->name === DriverStatusEnum::STATUS_ON_TRIP;
    }

    public function isOffline(): bool
    {
        return $this->status->name === DriverStatusEnum::STATUS_OFFLINE;
    }

    public function isAccountSuspended(): bool
    {
        return $this->isSuspended();
    }

    public function isProfileComplete(): bool
    {
        return $this->first_name != null
            && $this->last_name != null && $this->driver_license_photo != null && $this->profile_photo != null;
    }
    public function isDriverCarComplete(): bool
    {
        return $this->driverCar()->exists() !== false;
    }
    // =================
    // Business Logic
    // =================

    public function isNotifiedForTrip(Trip $trip): bool
    {
        return $this->tripNotifications()
            ->where('trip_id', $trip->id)
            ->exists();
    }

    public function updatePhoto(DriverPhotoType $type, UploadedFile $file): bool
    {
        $column = $type->value . '_photo';

        try {
            $service = $type->serviceMethod();

            // Delete old file if exists
            if ($this->getRawOriginal($column)) {
                $service->delete($this->getRawOriginal($column));
            }

            // Upload new file
            $path = "{$this->id}/";
            $url = $service->uploadPublic($file, $path);

            // Update model with file path
            $this->{$column} = $service->getFilePath($url);
            return $this->save();
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function deletePhoto(DriverPhotoType $type): bool
    {
        $column = $type->value . '_photo';
        $path = $this->getRawOriginal($column);

        if (!$path) return true;

        try {
            $service = $type->serviceMethod();
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

        foreach (DriverPhotoType::cases() as $type) {
            $service = $type->serviceMethod();
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


    /**
     * Custom suspend method that extends the trait functionality
     */

    public function suspendForever($suspendId): void
    {
        // Call the trait's original method
        $this->traitSuspendForever(suspendId: $suspendId);

        // Custom logic after suspension
        $this->setStatus(DriverStatusEnum::STATUS_OFFLINE);
    }

    public function suspendTemporarily($suspendId, Carbon $suspended_until): void
    {
        // Call the trait's original method
        $this->traitSuspendTemp(suspendId: $suspendId, suspended_until: $suspended_until);

        // Custom logic after suspension
        $this->setStatus(DriverStatusEnum::STATUS_OFFLINE);
    }

    public function reinstate(): void
    {
        // Call the trait's original method
        $this->traitReinstate();
        $this->setStatus(DriverStatusEnum::STATUS_OFFLINE);
    }

    public function setStatus(DriverStatusEnum $status): void
    {
        $statusId = DriverStatus::firstWhere('name', $status)?->id;
        if ($statusId) {
            $this->driver_status_id = $statusId;
            $this->save();
        }
    }
    public function setLocation(Point $point)
    {
        $this->location = $point;
        $this->save();
    }

    public function recalculateRating(): void
    {
        $average = $this->completedTrips()
            ->whereNotNull('driver_rating')
            ->avg('driver_rating');

        $this->update(['avg_rating' => $average ?? $this->avg_rating]);
    }
}
