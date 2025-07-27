<?php

namespace App\Http\Resources;

use App\Services\FileServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverCarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $assetService = FileServiceFactory::makeForDriverCarPhotos();

        return [
            'car_model' => new CarModelResource($this->whenLoaded('carModel')),

            'front_photo' => $this->front_photo,
            'back_photo' => $this->back_photo,
            'left_photo' => $this->left_photo,
            'right_photo' => $this->right_photo,
            'inside_photo' => $this->inside_photo,

            'dates' => [
                'created' => $this->created_at,
                'updated' => $this->updated_at,
            ],
        ];
    }
}
