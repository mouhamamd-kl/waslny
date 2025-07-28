<?php

namespace App\Http\Requests;

use App\Models\Country;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                $this->isRequired(),
                'string',
                'unique:' . PaymentMethod::class
            ],
            'is_active' => [
                $this->isRequired(),
                'boolean'
            ]
        ];
    }
}
