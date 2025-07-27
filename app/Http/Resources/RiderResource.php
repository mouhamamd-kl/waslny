<?php

namespace App\Http\Resources;

use App\Services\FileServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $assetService = FileServiceFactory::makeForRiderProfile();
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'profile_photo' => $this->when($this->profile_photo, function () use ($assetService) {
                return $assetService->getUrl($this->profile_photo);
            }, null),
            'rating' => $this->rating,
            'dates' => [
                'created' => $this->created_at,
                'updated' => $this->updated_at,
            ],
        ];
    }
}
