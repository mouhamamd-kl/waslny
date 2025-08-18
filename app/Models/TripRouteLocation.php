<?php

namespace App\Models\domains\trips\trip_route_locations;

use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Model;

class TripRouteLocation extends Model
{
    protected $table = 'trip_route_locations';

    protected $guarded = ['id'];

    protected $casts = [
        'location' => Point::class,
    ];

    public function trip()
    {
        return $this->belongsTo(\App\Models\Trip::class);
    }
}
