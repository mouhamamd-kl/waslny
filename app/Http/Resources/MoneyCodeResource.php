<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoneyCodeResource extends JsonResource
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
            'code' => $this->code,
            'value' => $this->value,
            'used' => $this->used_at !== null,
            'used_at' => $this->used_at,
            'rider' => new RiderResource($this->whenLoaded('rider')),
        ];
    }
}
