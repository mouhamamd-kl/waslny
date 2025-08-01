<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiderCouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'coupon' => new CouponResource($this->whenLoaded('coupon')),
            'dates' => [
                'created' => $this->created_at,
                'updated' => $this->updated_at,
            ],
        ];
    }
}
