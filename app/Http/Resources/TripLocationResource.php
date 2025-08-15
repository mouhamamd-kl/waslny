<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'location' => $this->when($this->location, function () {
                return [
                    'type' => 'Point',
                    'coordinates' => [
                        'lat' => $this->location->getLatitude(),
                        'long' => $this->location->getLongitude(),
                    ],
                ];
            }),
            'location_order' => $this->location_order,
            'location_type' => $this->location_type,
            'is_completed' => $this->is_completed,
            'estimated_arrival_time' => $this->estimated_arrival_time,
            'actual_arrival_time' => $this->actual_arrival_time,
            'dates' => [
                'created' => $this->created_at,
                'updated' => $this->updated_at,
            ],
        ];
    }
}
