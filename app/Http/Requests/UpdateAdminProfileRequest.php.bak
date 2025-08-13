<?php

namespace App\Http\Requests;

use App\Enums\FieldRequirementEnum;
use App\Models\CarManufacturer;
use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UpdateAdminProfileRequest extends BaseRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
    // public function authorize(): bool
    // {
    //     return auth('admin-api')->check();
    // }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function handleAuthorization(): bool
    {
        return auth('admin-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $admin = auth('admin-api')->user();

        return [
            'user_name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                Rule::unique('admins')->ignore($admin->id),
            ],
            'phone' => [
                'sometimes',
                'string',
                Rule::unique('admins')->ignore($admin->id),
            ],
            'profile_photo' => $this->imageRule(FieldRequirementEnum::SOMETIMES),
            'current_password' => [
                'sometimes',
                'string',
                'required_with:new_password',
                function ($attribute, $value, $fail) use ($admin) {
                    if (!Hash::check($value, $admin->password)) {
                        $fail(__('messages.auth.invalid_current_password'));
                    }
                },
            ],
            'new_password' => [
                'sometimes',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('new_password') && empty($this->current_password)) {
            $this->merge(['current_password' => '']);
        }
    }
}
