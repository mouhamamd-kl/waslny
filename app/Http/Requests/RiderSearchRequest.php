<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiderSearchRequest extends FormRequest
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
            'max_rating' => 'sometimes|numeric|min:1|max:5',
            'min_rating' => 'sometimes|numeric|min:1|max:5|lte:max_rating',
            'created_at' => 'sometimes|date',
        ];
    }
}
