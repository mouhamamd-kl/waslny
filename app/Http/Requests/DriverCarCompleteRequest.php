<?php

namespace App\Http\Requests;

use App\Enums\FieldRequirementEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverCarCompleteRequest extends BaseRequest
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
            'front_photo' => $this->imageRule(FieldRequirementEnum::REQUIRED),
            'back_photo' => $this->imageRule(FieldRequirementEnum::REQUIRED),
            'left_photo' => $this->imageRule(FieldRequirementEnum::REQUIRED),
            'right_photo' => $this->imageRule(FieldRequirementEnum::REQUIRED),
            'inside_photo' => $this->imageRule(FieldRequirementEnum::REQUIRED),
            'car_model_id' => [
                'required|exists:car_models,id'
            ]
        ];
    }
}
