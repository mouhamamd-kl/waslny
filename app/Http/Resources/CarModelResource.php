<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarModelResource extends JsonResource
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
            'name' => $this->name,
            'manufacturer' => new CarManufacturerResource($this->whenLoaded('manufacturer')),
            'service_level' => new CarServiceLevelResource($this->whenLoaded('serviceLevel')),
            'model_year' => $this->model_year,
            'is_active' => $this->is_active,
            'dates' => [
                'created' => $this->created_at,
                'updated' => $this->updated_at,
            ],
        ];
    }
}
