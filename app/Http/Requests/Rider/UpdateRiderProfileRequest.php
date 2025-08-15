<?php

namespace App\Http\Requests\Rider;

use App\Enums\FieldRequirementEnum;
use App\Http\Requests\BaseRequest;
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
            'first_name' => ['sometimes',  'nullable', 'string'],
            'last_name' => ['sometimes',   'nullable', 'string'],
            'phone' => [
                'sometimes',
                'nullable',
                'string',
                Rule::unique('riders')->ignore($rider->id),
            ],
            'email' => ['sometimes',  'nullable', 'email', Rule::unique('riders')->ignore($rider->id),],
            'profile_photo' => $this->imageRule(FieldRequirementEnum::SOMETIMES),
            'payment_method_id' => ['sometimes',  'nullable', "exists:payment_methods,id"],
            'location' => ['sometimes',   'nullable', 'string'],
        ];
    }
}
