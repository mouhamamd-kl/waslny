<?php

namespace App\Http\Requests\Suspension;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class SuspensionSearchRequest extends BaseRequest
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
            'reason' => 'sometimes|nullable|string|max:255',
            'admin_msg' => 'sometimes|nullable|string|max:255',
            'user_msg' => 'sometimes|nullable|string|max:255',
            'is_active' => 'sometimes|nullable|boolean',
        ];
    }
}
