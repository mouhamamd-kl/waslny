<?php

namespace App\Http\Requests;

use App\Enums\FieldRequirementEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverCarRequest extends BaseRequest
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
            'front_photo' => ['sometimes', $this->imageRule(FieldRequirementEnum::SOMETIMES),],
            'back_photo' => ['sometimes', $this->imageRule(FieldRequirementEnum::SOMETIMES),],
            'left_photo' => ['sometimes', $this->imageRule(FieldRequirementEnum::SOMETIMES),],
            'right_photo' => ['sometimes', $this->imageRule(FieldRequirementEnum::SOMETIMES),],
            'inside_photo' => ['sometimes', $this->imageRule(FieldRequirementEnum::SOMETIMES),],
            'car_model_id' => [
                'sometimes|exists:car_models,id'
            ]
        ];
    }
}
