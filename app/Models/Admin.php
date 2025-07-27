<?php

namespace App\Models;

use App\Notifications\AdminResetPassword;
use App\Services\BaseFileService;
use App\Services\FileServiceFactory;
use App\Traits\General\FilterScope;
use App\Traits\General\ResetOTP;
use App\Traits\General\TwoFactorCodeGenerator;
use Exception;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;

enum AdminPhotoType: string
{
    case PROFILE = 'profile';
    public function serviceMethod(): BaseFileService
    {
        return match ($this) {
            self::PROFILE => FileServiceFactory::makeForAdminProfile(),
        };
    }
}
class Admin extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorCodeGenerator, FilterScope, HasApiTokens, ResetOTP;

    // =================
    // Configuration
    // =================
    protected $table = 'admins';
    protected $primaryKey = 'id'; // Explicitly define since it's BIGINT
    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'two_factor_expires_at' => 'datetime',
        'avg_rating' => 'float',
    ];
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPassword($token));
    }


    public function generateTwoFactorCode(): void
    {
        $this->two_factor_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->two_factor_expires_at = now()->addMinutes(5);
        $this->save();
    }

    public function resetTwoFactorCode(): void
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    // =================
    // Business Logic
    // =================

    public function updatePhoto(AdminPhotoType $type, UploadedFile $file): bool
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

    public function deletePhoto(AdminPhotoType $type): bool
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

        foreach (AdminPhotoType::cases() as $type) {
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
}
