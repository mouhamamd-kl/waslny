<?php

namespace App\Http\Requests\Suspension;

use App\Http\Requests\BaseRequest;
use App\Models\Suspension;
use Illuminate\Foundation\Http\FormRequest;

class SuspensionStoreRequest extends BaseRequest
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
                'required',
                'string',
                'unique:' . Suspension::class . ',reason,' . $suspensionId
            ],
            'admin_msg' => [
                'required',
                'string',
            ],
            'user_msg' => [
                'required',
                'string',
            ],
            'is_active' => [
                'required',
                'boolean',
            ]
        ];
    }
}
