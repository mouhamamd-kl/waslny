<?php

namespace App\Rules;

use App\Models\Coupon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveCoupon implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var Coupon $coupon */
        $coupon = Coupon::find($value);
        if (!$coupon) {
            $fail('The selected coupon does not exist.');
            return;
        }
        if (!$coupon->isActive()) {
            $fail('The selected coupon is not active.');
        }
        // Optional additional checks
        if ($coupon->isExpired()) {
            $fail('This coupon has expired.');
        }
    }

    public function message()
    {
        return 'The coupon is not active.';
    }
}
