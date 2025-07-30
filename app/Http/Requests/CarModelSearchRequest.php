<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarModelSearchRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'car_service_level_id' => 'sometimes|integer|exists:car_service_levels,id',
            'car_manufacture_id' => 'sometimes|integer|exists:car_manufacturers,id',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
