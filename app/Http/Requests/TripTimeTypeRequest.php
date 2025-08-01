<?php

namespace App\Http\Requests;

use App\Models\Country;
use App\Models\PaymentMethod;
use App\Models\TripTimeType;
use App\Models\TripType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TripTimeTypeRequest extends BaseRequest
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
        return [
            'name' => [
                $this->isRequired(),
                'string',
                'unique:' . TripTimeType::class
            ],
            'description' => [
                $this->isRequired(),
                'string',
            ],
            'is_active' => [
                $this->isRequired(),
                'boolean'
            ]
        ];
    }
}
