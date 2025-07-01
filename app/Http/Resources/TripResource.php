<?php

namespace App\Http\Resources;

use App\Models\TripStatus;
use App\Models\TripTimeType;
use App\Models\TripType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
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
            'driver' => new DriverResource($this->whenLoaded('driver')),
            'rider' => new RiderResource($this->whenLoaded('rider')),
            'status' => new TripStatus($this->whenLoaded('status')),
            'type' => new TripType($this->whenLoaded('type')),
            'time_type' => new TripTimeType($this->whenLoaded('timeType')),
            'location' => new TripLocationResource($this->whenLoaded('locations')),
            'coupon' => $this->whenLoaded('coupon'),
            'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration_minutes' => $this->durationInMinutes(),
            'distance' => [
                'meters' => $this->distance,
                'kilometers' => $this->distanceInKm(),
            ],
            'fare' => [
                'amount' => $this->fare,
            ],
        ];
    }
}
