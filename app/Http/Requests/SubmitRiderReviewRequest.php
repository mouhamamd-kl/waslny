<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRiderReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
