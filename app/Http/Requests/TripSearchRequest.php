<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripSearchRequest extends FormRequest
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
            'trip_status_id' => 'sometimes|nullable|integer|exists:trip_statuses,id',
            'trip_type_id' => 'sometimes|nullable|integer|exists:trip_types,id',
            'trip_time_type_id' => 'sometimes\nullable|integer|exists:trip_time_types,id',
            'start_time' => 'sometimes|nullable|date',
            'end_time' => 'sometimes|nullable|date|after_or_equal:start_time',
            'fare' => 'sometimes|nullable|numeric',
        ];
    }
}
