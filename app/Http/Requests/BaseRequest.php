<?php

namespace App\Http\Requests;

use App\Enums\FieldRequirementEnum;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class BaseRequest extends FormRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  *
    //  * @return bool
    //  */
    // public function authorize()
    // {
    //     return true;
    // }

    /**
     * Global authorization rules with console bypass
     */
    final public function authorize(): bool
    {
        return $this->isConsoleExecution()
            ? true
            : $this->handleAuthorization();
    }

    /**
     * Handle non-console authorization
     */
    protected function handleAuthorization(): bool
    {
        return true;
    }

    /**
     * Determine if execution is in console context
     */
    final function isConsoleExecution(): bool
    {
        return is_console();
    }

    /**
     * this function resolve validation error message
     *

     *
     * @return void
     */

    protected function failedValidation(Validator $validator)
    {
        $errors = [];
        foreach ($validator->errors()->toArray() as $field => $messages) {
            $errors[$field] = array_map(fn($msg) => trans_fallback($msg, $msg), $messages);
        }
        // ksort($errors);
        $firstError = collect($errors)->flatten()->first()
            ?? trans_fallback('messages.validation_failed', 'Validation failed');
        throw new HttpResponseException(
            ApiResponse::sendResponseError(
                message: trans_fallback('messages.validation_failed', 'Validation failed'),
                statusCode: 422,
                data: ['first_error' => $firstError, 'errors' => $errors]
            )
        );
    }

    /**
     * this function to check if request is update request
     *
     * @return bool
     */
    public function isUpdatedRequest()
    {
        return request()->isMethod('PUT') || request()->isMethod('PATCH');
    }

    /**
     * this function to return all required rule for an image
     *
     * @return string
     */
    public function imageRule(?FieldRequirementEnum $requirement): string
    {
        $requirement = $requirement ?? $this->isRequired(); // Fallback to required()
        return "{$requirement->value}|mimes:jpeg,png,jpg,gif,webp|max:2048";
    }

    /**
     * this function to return all required rule for date request parameter
     *
     * @return string
     */
    // Real Estate Specific Helpers
    public function priceRules(?FieldRequirementEnum $requirement)
    {
        $requirement = $requirement ?? $this->isRequired(); // Fallback to required()
        return "{$this->isRequired()}|numeric|min:0|max:999999999.99";
    }

    public function dateRules(?FieldRequirementEnum $requirement)
    {
        $requirement = $requirement ?? $this->isRequired(); // Fallback to required()
        return "{$this->isRequired()}|after:now";
    }

    /**
     * check if the request is update request then don't verify if the request key is required.
     *
     * @return string
     */
    public function isRequired()
    {
        return $this->isUpdatedRequest() ? FieldRequirementEnum::SOMETIMES->value : FieldRequirementEnum::REQUIRED->value;
    }

    public function coordinateRules()
    {
        return "{$this->required()}|numeric|between:-180,180";
    }

    public function slugRule()
    {
        return "{$this->required()}|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/";
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('is_active') && $this->input('is_active') != null) {
            $isActive = filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($isActive !== null) {
                $this->merge(['is_active' => $isActive]);
            }
        } else {
            $this->request->remove('is_active');
        }
    }
}
