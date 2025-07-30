<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverSearchRequest extends FormRequest
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
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'driver_status_id' => 'sometimes|integer|exists:driver_statuses,id',
            'max_rating' => 'sometimes|numeric|min:1|max:5',
            'min_rating' => 'sometimes|numeric|min:1|max:5|lt:max_rating',
            'created_at' => 'sometimes|date',
        ];
    }
}
