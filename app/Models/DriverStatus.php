<?php

namespace App\Models;

use App\Traits\General\FilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverStatus extends Model
{
    use FilterScope;
    // =================
    // Configuration
    // =================
    protected $table = 'driver_statuses';
    protected $guarded = ['id']; // or use `protected $fillable = ['name'];`

    // =================
    // Relationships
    // =================
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    // =================
    // Scopes
    // =================
    
}
