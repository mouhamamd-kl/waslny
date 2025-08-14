<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\BaseRequest;
use App\Models\Country;
use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponUpdateRequest extends BaseRequest
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
            'code' => [
                'sometimes',
                'nullable',
                'string',
                'unique:' . Coupon::class
            ],
            'max_uses' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1'
            ],
            'percent' => [
                'sometimes',
                'nullable',
                'decimal:2',
                'min:0.1',
                'max:0.9',
            ],
            'start_date' => [
                'sometimes',
                'nullable',
                Rule::date()->afterOrEqual(now()),
            ],
            'end_date' => [
                'sometimes',
                'nullable',
                Rule::date()->after(now()),
            ],
            'is_active' => [
                'sometimes',
                'nullable',
                'boolean'
            ]
        ];
    }
}
