<?php

namespace App\Http\Requests;

use App\Models\CarModel;
use App\Models\Country;
use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarModelRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return auth('admin-api')->check();
    // }

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
                'unique:' . CarModel::class
            ],
            'car_manufacture_id' => [
                $this->isRequired(),
                'exists:car_manufacturers,id'
            ],
            'car_service_level_id' => [
                $this->isRequired(),
                'exists:car_service_levels,id'
            ],
            'year' => [
                $this->isRequired(),
                'integer',
                'min:2003',
                'max:' . now()->year + 1
            ],
            'is_active' => [
                $this->isRequired(),
                'boolean'
            ]
        ];
    }
}
