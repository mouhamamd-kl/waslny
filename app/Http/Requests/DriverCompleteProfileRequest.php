<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverCompleteProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('driver-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'national_number' => ['required', 'string'],
            'profile_photo' => $this->imageRule(),
            'driver_license_photo' => $this->imageRule(),
            'gender' => [
                'required',
                'string',
                Rule::in(['male', 'female']), // Dynamic from Enum
            ],
        ];
    }
}
