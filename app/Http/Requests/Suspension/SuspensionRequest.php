<?php

namespace App\Http\Requests\Suspension;

use App\Http\Requests\BaseRequest;
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
        $suspensionId = $this->route('suspension');

        return [
            'reason' => [
                $this->isRequired(),
                'nullable',
                'string',
                'unique:' . Suspension::class . ',reason,' . $suspensionId
            ],
            'admin_msg' => [
                $this->isRequired(),
                'nullable',
                'string',
            ],
            'user_msg' => [
                $this->isRequired(),
                'nullable',
                'string',
            ],
            'is_active' => [
                $this->isRequired(),
                'nullable',
                'boolean',
            ]
        ];
    }
}
