<?php

namespace App\Models\domains\trips\trip_route_locations;

use Illuminate\Database\Eloquent\Model;

class TripRouteLocation extends Model
{
    protected $table = 'trip_route_locations';

    protected $fillable = [
        'trip_id',
        'location',
    ];

    public function trip()
    {
        return $this->belongsTo(\App\Models\Trip::class);
    }
}
