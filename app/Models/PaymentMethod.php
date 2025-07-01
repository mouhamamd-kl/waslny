<?php

namespace App\Models;

use App\Services\FileServiceFactory;
use App\Traits\General\ActiveScope;
use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use FilterScope, ActiveScope;
    // =================
    // Configuration
    // =================
    protected $table = 'payment_methods';
    protected $guarded = ['id'];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    // =================
    // Relationships
    // =================
    public function riders(): HasMany
    {
        return $this->hasMany(Rider::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    // =================
    // Accessors & Mutators
    // =================
    protected function icon(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $this->getPhotoUrl($value),
            set: fn(?string $value) => $value
        );
    }
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
    // Scopes
    // =================


    // =================
    // Business Logic
    // =================
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
