<?php

namespace App\Http\Requests;

use App\Enums\FieldRequirementEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverProfileRequest extends BaseRequest
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
        $driver = auth('driver-api')->user();
        return [
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'phone' => [
                'sometimes',
                'string',
                Rule::unique('drivers')->ignore($driver->id),
            ],
            'email' => ['sometimes', 'email',  Rule::unique('drivers')->ignore($driver->id),],
            'national_number' => ['sometimes', 'string',  Rule::unique('drivers')->ignore($driver->id),],
            'profile_photo' => $this->imageRule(FieldRequirementEnum::SOMETIMES),
            'driver_license_photo' => $this->imageRule(FieldRequirementEnum::SOMETIMES),
            'gender' => [
                'sometimes',
                'string',
                Rule::in(['male', 'female']), // Dynamic from Enum
            ],
        ];
    }
}
