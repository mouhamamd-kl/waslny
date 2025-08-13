<?php

namespace App\Http\Requests;

use App\Models\Suspension;
use Illuminate\Foundation\Http\FormRequest;

class SuspensionRequest extends BaseRequest
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
            'reason' => [
                $this->isRequired(),
                'string',
                'unique:' . Suspension::class
            ],
            'admin_msg' => [
                $this->isRequired(),
                'string',
            ],
            'user_msg' => [
                $this->isRequired(),
                'string',
            ],
            'is_active' => [
                $this->isRequired(),
                'boolean',
            ]
        ];
    }
}
