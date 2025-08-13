<?php

namespace App\Http\Requests\Pricing;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class PricingSearchRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function handleAuthorization(): bool
    {
        return auth('admin-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'car_service_level_id' => 'sometimes|nullable|integer|exists:car_service_levels,id',
            'min_price_per_km' => 'sometimes|nullable|numeric',
            'max_price_per_km' => 'sometimes|nullable|numeric',
            'is_active' => 'sometimes|nullable|boolean',
        ];
    }
}
