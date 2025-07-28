<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSuspensionRequest extends FormRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
    // public function authorize(): bool
    // {
    //     return auth('admin-api')->check();;
    // }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function handleAuthorization(): bool
    {
        return auth('admin-api')->check();;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer'],
            'user_type' => ['required', 'string', Rule::in(['driver', 'rider'])],
            'suspension_id' => ['required', 'integer', 'exists:suspensions,id'],
            'suspended_until' => ['nullable', 'date'],
            'is_permanent' => ['boolean'],
        ];
    }
}
