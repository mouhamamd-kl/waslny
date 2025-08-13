<?php

namespace App\Http\Requests\CarManufacturer;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class CarManufacturerDriverSearchRequest extends BaseRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|nullable|string|max:255',
        ];
    }
}
