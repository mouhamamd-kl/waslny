<?php

namespace App\Http\Requests\DriverStatus;

use App\Http\Requests\BaseRequest;
use App\Models\CarModel;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\DriverStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverStatusUpdateRequest extends BaseRequest
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
                'sometimes',
                'nullable',
                'string',
                'unique:' . DriverStatus::class
            ],
            'is_active' => [
                'sometimes',
                'nullable',
                'boolean'
            ]
        ];
    }
}
