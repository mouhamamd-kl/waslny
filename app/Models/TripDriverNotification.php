<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripDriverNotification extends Model
{
    protected $table = 'trip_driver_notifications';
    protected $guarded = ['id'];
    protected $casts = [
        'sent_at' => 'datetime'
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
