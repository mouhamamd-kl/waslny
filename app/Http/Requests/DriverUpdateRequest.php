<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverUpdateRequest extends BaseRequest
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
            'first_name' => [$this->isRequired(), 'string'],
            'last_name' => [$this->isRequired(), 'string'],
            'phone' => [
                $this->isRequired(),
                'string'
            ],
            'email' => [$this->isRequired(), 'email'],
            'national_number' => [$this->isRequired(), 'string'],
            'profile_photo' => $this->imageRule(FieldRequirement::SOMETIMES),
            'driver_license_photo' => $this->imageRule(FieldRequirement::SOMETIMES),
            'gender' => [
                $this->isRequired(),
                'string',
                Rule::in(['male', 'female']), // Dynamic from Enum
            ],
        ];
    }
}
