<?php

namespace App\Http\Resources;

use App\Services\FileServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuspensionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reason' => $this->reason,
            'admin_msg' => $this->admin_msg,
            'user_msg' => $this->user_msg,
            'is_active' => $this->is_active,
            'dates' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
