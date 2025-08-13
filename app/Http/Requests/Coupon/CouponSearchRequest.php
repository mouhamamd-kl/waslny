<?php

namespace App\Http\Requests\Coupon;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class CouponSearchRequest extends BaseRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'sometimes|nullable|string|max:255',
            'start_date' => 'sometimes|nullable|date',
            'end_date' => 'sometimes|nullable|date|after_or_equal:start_date',
            'max_max_uses' => 'sometimes|nullable|integer',
            'min_max_uses' => 'sometimes|nullable|integer|lte:max_max_uses',
            'max_used_count' => 'sometimes|nullable|integer',
            'min_used_count' => 'sometimes|nullable|integer|lte:max_used_count',
            'max_percent' => 'sometimes|nullable|decimal:2',
            'min_percent' => 'sometimes|nullable|decimal:2|lte:max_percent',
            'is_active' => 'sometimes|nullable|boolean',
        ];
    }
}
