<?php

namespace App\Rules;

use App\Models\Coupon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TripLocationRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = is_string($value) ? json_decode($value, true) : $value;

        if (!is_array($data)) {
            $fail('Invalid JSON structure.');
        }

        if (empty($data['location'])) {
            $fail('The location is missing.');
        }

        if (empty($data['location_order'])) {
            $fail('The location order is missing.');
        }
    }

    public function message()
    {
        return 'The coupon is not active.';
    }
}
