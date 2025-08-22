<?php

namespace App\Http\Resources;

use App\Services\FileServiceFactory;
use App\Traits\Resource\ResolvesPolymorphicResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountSuspensionResource  extends JsonResource
{
    use ResolvesPolymorphicResource;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // 'suspendable' => $this->whenLoaded('suspendable', function () {
            //     return $this->resolvePolymorphicResource($this->suspendable);
            // }),
            'suspension' => new SuspensionResource($this->whenLoaded('suspension')),
            'lifted_at' => $this->lifted_at,
            'is_permanent' => $this->is_permanent,
            'suspended_until' => $this->suspended_until,
            'dates' => [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]
        ];
    }
}
