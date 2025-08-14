<?php

namespace App\Http\Requests\CarServiceLevel;

use App\Http\Requests\BaseRequest;
use App\Models\CarManufacturer;
use App\Models\CarServiceLevel;
use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarServiceLevelUpdateRequest extends BaseRequest
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
                'sometimes',
                'nullable',
                'string',
                'unique:' . CarServiceLevel::class
            ],
            'is_active' => [
                'sometimes',
                'nullable',
                'boolean'
            ]
        ];
    }
}
