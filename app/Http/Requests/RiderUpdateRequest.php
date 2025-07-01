<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiderUpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
        return [
            'first_name' => [$this->isRequired(), 'string'],
            'last_name' => [$this->isRequired(), 'string'],
            'phone' => [
                $this->isRequired(),
                'string'
            ],
            'email' => [$this->isRequired(), 'email'],
            'profile_photo' => $this->imageRule(FieldRequirement::SOMETIMES),
            'payment_method_id' => $this->isRequired() . "|exists:payment_methods,id"
        ];
    }
}
