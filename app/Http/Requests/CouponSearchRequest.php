<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponSearchRequest extends FormRequest
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
            'code' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'max_max_uses' => 'sometimes|integer',
            'min_max_uses' => 'sometimes|integer|lte:max_max_uses',
            'max_used_count' => 'sometimes|integer',
            'min_used_count' => 'sometimes|integer|lte:max_used_count',
            'max_percent' => 'sometimes|decimal:2',
            'min_percent' => 'sometimes|decimal:2|lte:max_percent',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
