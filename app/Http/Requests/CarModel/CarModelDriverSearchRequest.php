<?php

namespace App\Http\Requests\CarModel;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class CarModelDriverSearchRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function handleAuthorization(): bool
    {
        return auth('driver-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|nullable|string|max:255',
            'car_service_level_id' => 'sometimes|nullable|integer|exists:car_service_levels,id',
            'car_manufacture_id' => 'sometimes|nullable|integer|exists:car_manufacturers,id',
            'is_active' => 'sometimes|nullable|boolean',
        ];
    }
}
