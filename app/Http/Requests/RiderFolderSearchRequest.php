<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiderFolderSearchRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function handleAuthorization(): bool
    {
        return true;
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
            'rider_id' => 'sometimes|nullable|integer|exists:riders,id',
        ];
    }
}
