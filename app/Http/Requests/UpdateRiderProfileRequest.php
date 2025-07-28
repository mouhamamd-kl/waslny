<?php

namespace App\Http\Requests;

use App\Enums\FieldRequirementEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRiderProfileRequest extends BaseRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
    // public function authorize(): bool
    // {
    //     return auth('rider-api')->check();
    // }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function handleAuthorization(): bool
    {
        return auth('rider-api')->check();
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rider = auth('rider-api')->user();
        return [
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'phone' => [
                'string',
                'sometimes',
                Rule::unique('riders')->ignore($rider->id),
            ],
            'email' => ['sometimes', 'email', Rule::unique('riders')->ignore($rider->id),],
            'profile_photo' => $this->imageRule(FieldRequirementEnum::SOMETIMES),
            'payment_method_id' => 'sometimes' . "|exists:payment_methods,id",
            'location' => ['sometimes', 'string'],
        ];
    }
}
