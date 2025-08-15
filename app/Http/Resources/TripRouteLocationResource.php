<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripRouteLocationResource extends JsonResource
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
            'dates' => [
                'created' => $this->created_at,
                'updated' => $this->updated_at,
            ],
        ];
    }
}
