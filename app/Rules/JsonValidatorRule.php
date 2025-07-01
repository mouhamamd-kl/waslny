<?php

namespace App\Rules;

use App\Models\Coupon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JsonValidatorRule implements ValidationRule
{
    public function __construct(
        private array $requiredFields,
        private ?Closure $customValidator = null
    ) {}
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $data = is_string($value) ? json_decode($value, true) : $value;

        if (count($this->requiredFields) !== count($data)) {
            $fail("The object contains unexpected fields. Expected: " . implode(', ', $this->requiredFields));
        } else {

            foreach ($this->requiredFields as $field) {
                if (!array_key_exists($field, $data)) {
                    $fail("$attribute must contain a $field");
                }
            }
        }
    }
}
