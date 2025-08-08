<?php

namespace App\Models;

use App\Services\BaseFileService;
use App\Services\FileServiceFactory;
use App\Traits\General\FilterScope;
use App\Traits\General\ResetOTP;
use App\Traits\General\Suspendable;
use App\Traits\General\TwoFactorCode;
use App\Traits\HasDeviceToken;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWallet;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

enum RiderPhotoType: string
{
    case PROFILE = 'profile';
    public function serviceMethod(): BaseFileService
    {
        return match ($this) {
            self::PROFILE => FileServiceFactory::makeForRiderProfile(),
        };
    }
}
class Rider  extends Authenticatable implements Wallet
{
    // =================
    // Traits
    // =================

    use HasFactory, Notifiable, HasApiTokens, TwoFactorCode, FilterScope, ResetOTP, Suspendable, HasWallet, HasDeviceToken;

    // =================
    // Configuration
    // =================

    protected $table = 'riders';
    protected $primaryKey = 'id'; // Explicitly define since it's BIGINT

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'two_factor_expires_at' => 'datetime',
        'avg_rating' => 'float',
    ];

    // =================
    // Relationships
    // =================

    /**
     * Default payment method for the rider
     */
    public function defaultPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'defaul_payment_id');
    }

    /**
     * Saved locations for the rider
     */
    public function savedLocations(): HasMany
    {
        return $this->hasMany(RiderSavedLocation::class, 'rider_id');
    }

    /**
     * Folders created by the rider
     */
    public function folders(): HasMany
    {
        return $this->hasMany(RiderFolder::class, 'rider_id');
    }

    /**
     * Trips taken by the rider
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'rider_id');
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
    /**
     * Coupons owned by the rider (through pivot)
     */
    public function Ridercoupons()
    {
        return $this->hasMany(RiderCoupon::class);
    }
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'rider_coupons', 'rider_id', 'coupon_id');
    }

    public function suspensions()
    {
        return $this->morphMany(AccountSuspension::class, 'suspendable');
    }

    // =================
    // Accessors & Mutators
    // =================

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function setPhoneNumberAttribute($value): void
    {
        $this->attributes['phone_number'] = preg_replace('/[^0-9+]/', '', $value);
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo) {
            return null;
        }

        return filter_var($this->profile_photo, FILTER_VALIDATE_URL)
            ? $this->profile_photo
            : FileServiceFactory::makeForRiderProfile()->getUrl($this->profile_photo);
    }

    // =================
    // Scopes
    // =================

    //TODO make this work idiot bitch
    // public function scopeActive(Builder $query): Builder
    // {
    //     return $query->where('suspended', false);
    // }

    // public function scopeSuspended(Builder $query): Builder
    // {
    //     return $query->where('suspended', true);
    // }

    // =================
    // Business Logic
    // =================
    public function updatePhoto(RiderPhotoType $type, UploadedFile $file): bool
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

    public function deletePhoto(RiderPhotoType $type): bool
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

    public function generateTwoFactorCode(): void
    {
        $this->two_factor_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->two_factor_expires_at = now()->addMinutes(5);
        $this->save();
    }

    /**
     * Update rider rating based on completed trips
     */
    public function recalculateRating(): void
    {
        $average = $this->trips()
            ->whereNotNull('rating')
            ->avg('rating');

        $this->update(['avg_rating' => $average ?? $this->avg_rating]);
    }

    /**
     * Check if rider has payment method set
     */
    public function isProfileComplete(): bool
    {
        return $this->first_name != null
            && $this->last_name != null;
    }

    public function hasPaymentMethod(): bool
    {
        return !is_null($this->defaul_payment_id);
    }

    public function isAccountSuspended(): bool
    {
        return $this->isSuspended();
    }
}
