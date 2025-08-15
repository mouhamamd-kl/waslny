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
            // 'driver' => new DriverResource($this->whenLoaded('driver')),
            'driver' => $this->when($this->driver()->exists(), function () {
                return  new DriverResource($this->whenLoaded('driver'));
            }, null),
            // 'rider' => $this->rider()->exists(),
            'rider' => $this->when($this->rider()->exists(), function () {
                return  new RiderResource($this->whenLoaded('rider'));
            }, null),
            // 'status' => new TripStatus($this->whenLoaded('status')),
            'status' => $this->when($this->status()->exists(), function () {
                return   new TripStatusResource($this->whenLoaded('status'));
            }, null),
            // 'type' => new TripType($this->whenLoaded('type')),
            'type' => $this->when($this->type()->exists(), function () {
                return  new TripTypeResource($this->whenLoaded('type'));
            }, null),
            // 'time_type' => new TripTimeType($this->whenLoaded('timeType')),
            'time_type' => $this->when($this->timeType()->exists(), function () {
                return  new TripTimeTypeResource($this->whenLoaded('timeType'));
            }, null),
            'locations'=>  TripLocationResource::collection($this->whenLoaded('locations')),
            'route_locations' => TripRouteLocationResource::collection($this->whenLoaded('routeLocations')),
            // 'location' => new TripLocationResource($this->whenLoaded('locations')),
            // 'locations' => $this->when($this->locations()->exists(), function () {
            //     return TripLocationResource::collection($this->whenLoaded('locations'));
            // }, []),
            // 'coupon' => $this->whenLoaded('coupon'),
            'coupon' => $this->when($this->riderCoupon()->exists(), function () {
                return   new CouponResource($this->whenLoaded('riderCoupon')->coupon());
            }, null),
            // 'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'payment_method' => $this->when($this->paymentMethod()->exists(), function () {
                return   new PaymentMethodResource($this->whenLoaded('paymentMethod'));
            }, null),
            'start_time' => $this->start_time,
            'requested_time' => $this->requested_time,
            'end_time' => $this->end_time,
            'duration_minutes' => $this->durationInMinutes(),
            'distance' => [
                'meters' => $this->distance,
                'kilometers' => $this->distanceInKm,
            ],
            'fare' => [
                'amount' => $this->fare,
            ],
        ];
    }
}
