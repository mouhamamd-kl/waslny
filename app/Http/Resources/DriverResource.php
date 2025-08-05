<?php

namespace App\Http\Resources;

use App\Services\FileServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $assetService = FileServiceFactory::makeForDriverProfile();
        $assetDriverLicense = FileServiceFactory::makeForDriverLicense();
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'national_number' => $this->national_number,
            'phone' => $this->phone,
            'email' => $this->email,
            'profile_photo' => $this->when($this->profile_photo, function () use ($assetService) {
                return $assetService->getUrl($this->profile_photo);
            }, null),
            'driver_license_photo' => $this->when($this->driver_license_photo, function () use ($assetDriverLicense) {
                return $assetDriverLicense->getUrl($this->driver_license_photo);
            }, null),
            'rating' => $this->rating,
            'wallet' => [
                'balance' => $this->balance,
            ],
            'driver_status' => new DriverStatusResource($this->whenLoaded('status')),
            'dates' => [
                'created' => $this->created_at,
                'updated' => $this->updated_at,
            ],
        ];
    }
}
