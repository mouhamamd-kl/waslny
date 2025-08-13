<?php

namespace App\Http\Requests\Trip;

use App\Http\Requests\BaseRequest;

class SubmitRiderReviewRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function handleAuthorization(): bool
    {
        return auth('rider-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'notes' => ['nullable', 'string'],
            'tip_amount' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
