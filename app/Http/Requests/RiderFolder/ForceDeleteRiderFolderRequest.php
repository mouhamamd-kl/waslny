<?php

namespace App\Http\Requests\RiderFolder;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class ForceDeleteRiderFolderRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function handleAuthorization(): bool
    {
        return auth('rider-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'force' => 'required|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'force' => filter_var($this->force, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
