<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PricingSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'car_service_level_id' => 'sometimes|integer|exists:car_service_levels,id',
            'min_price_per_km' => 'sometimes|numeric|lte:max_price_per_km',
            'max_price_per_km' => 'sometimes|numeric|gte:min_price_per_km',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
