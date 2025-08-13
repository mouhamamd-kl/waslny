<?php

namespace App\Http\Requests\MoneyCode;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class MoneyCodeRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|unique:money_codes,code',
            'value' => 'required|numeric|min:0',
        ];
    }
}
