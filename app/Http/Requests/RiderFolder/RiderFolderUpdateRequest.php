<?php

namespace App\Http\Requests\RiderFolder;

use App\Http\Requests\BaseRequest;
use App\Models\Country;
use App\Models\PaymentMethod;
use App\Models\TripType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiderFolderUpdateRequest extends BaseRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
    // public function authorize(): bool
    // {
    //     return auth('rider-api')->check();
    // }

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;
        return [
            'name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('rider_folders')
                    ->where('rider_id', auth('rider-api')->id())
                    ->ignore($this->route('rider_folder'))
            ],

        ];
    }
}
