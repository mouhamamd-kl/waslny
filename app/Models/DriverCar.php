<?php

namespace App\Models;

use App\Services\FileServiceFactory;
use App\Traits\General\FilterScope;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;

enum CarPhotoType: string
{
    case FRONT = 'front';
    case BACK = 'back';
    case LEFT = 'left';
    case RIGHT = 'right';
    case INSIDE = 'inside';
}

class DriverCar extends Model
{
    use FilterScope;
    // =================
    // Configuration
    // =================
    protected $table = 'driver_car';
    protected $guarded = ['id'];

    // =================
    // Attribute Casts (Modern Syntax)
    // =================
    protected function frontPhoto(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $this->getPhotoUrl($value),
            set: fn(?string $value) => $value
        );
    }

    protected function backPhoto(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $this->getPhotoUrl($value),
            set: fn(?string $value) => $value
        );
    }

    protected function leftPhoto(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $this->getPhotoUrl($value),
            set: fn(?string $value) => $value
        );
    }

    protected function rightPhoto(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $this->getPhotoUrl($value),
            set: fn(?string $value) => $value
        );
    }

    protected function insidePhoto(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $this->getPhotoUrl($value),
            set: fn(?string $value) => $value
        );
    }

    // =================
    // Helper Methods
    // =================
    protected function getPhotoUrl(?string $path): string
    {
        if (empty($path)) {
            return config('app.default_car_photo_url');
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        try {
            $service = FileServiceFactory::makeForDriverCarPhotos();
            return $service->getUrl($path);
        } catch (\Exception $e) {
            report($e);
            return config('app.default_car_photo_url');
        }
    }

    // =================
    // Relationships
    // =================
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    // =================
    // Scopes
    // =================
    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeByCar($query, $carId)
    {
        return $query->where('car_id', $carId);
    }

    public function scopeWithCompletePhotos($query)
    {
        foreach (CarPhotoType::cases() as $type) {
            $query->whereNotNull($type->value . '_photo');
        }
        return $query;
    }

    // =================
    // Business Logic
    // =================
    public function getPhotoUrlFor(CarPhotoType $type): string
    {
        return $this->{$type->value . '_photo'}; // Uses the accessor automatically
    }

    public function updatePhoto(CarPhotoType $type, UploadedFile $file): bool
    {
        $column = $type->value . '_photo';

        try {
            $service = FileServiceFactory::makeForDriverCarPhotos();

            // Delete old file if exists
            if ($this->getRawOriginal($column)) {
                $service->delete($this->getRawOriginal($column));
            }

            // Upload new file
            $path = "{$this->driver_id}";
            $url = $service->uploadPublic($file, $path);

            // Update model with file path
            $this->{$column} = $service->getFilePath($url);
            return $this->save();
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function deletePhoto(CarPhotoType $type): bool
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

        foreach (CarPhotoType::cases() as $type) {
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

    public function hasAllPhotos(): bool
    {
        foreach (CarPhotoType::cases() as $type) {
            if (empty($this->getRawOriginal($type->value . '_photo'))) {
                return false;
            }
        }
        return true;
    }

    public function getAllPhotoUrls(): array
    {
        return collect(CarPhotoType::cases())
            ->mapWithKeys(fn(CarPhotoType $type) => [
                $type->value => $this->getPhotoUrlFor($type)
            ])
            ->toArray();
    }
}
