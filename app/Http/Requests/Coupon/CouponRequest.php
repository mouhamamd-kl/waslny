<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\BaseRequest;
use App\Models\Country;
use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends BaseRequest
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
                $this->isRequired(),
                'string',
                'unique:' . Coupon::class
            ],
            'max_uses' => [
                $this->isRequired(),
                'integer',
                'min:1'
            ],
            'percent' => [
                $this->isRequired(),
                'decimal:2',
                'min:0.1',
                'max:0.9',
            ],
            'start_date' => [
                $this->isRequired(),
                Rule::date()->afterOrEqual(now()),
            ],
            'end_date' => [
                $this->isRequired(),
                Rule::date()->after(now()),
            ],
            'is_active' => [
                $this->isRequired(),
                'boolean'
            ]
        ];
    }
}
