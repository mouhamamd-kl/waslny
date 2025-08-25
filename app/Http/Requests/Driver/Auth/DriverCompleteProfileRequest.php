<?php

namespace App\Http\Requests\Driver\Auth;

use App\Enums\FieldRequirementEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverCompleteProfileRequest extends BaseRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
    // public function authorize(): bool
    // {
    //     return auth('driver-api')->check();
    // }

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'national_number' => ['required', 'string', 'numeric', Rule::unique('drivers')->ignore(auth('driver-api')->id())],
            'profile_photo' => $this->imageRule(FieldRequirementEnum::REQUIRED),
            'driver_license_photo' => $this->imageRule(FieldRequirementEnum::REQUIRED),
            'gender' => [
                'required',
                'string',
                Rule::in(['male', 'female']), // Dynamic from Enum
            ],
        ];
    }
}
