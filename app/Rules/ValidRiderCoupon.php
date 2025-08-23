<?php

namespace App\Rules;

use App\Models\RiderCoupon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class ValidRiderCoupon implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $riderCoupon = RiderCoupon::with('coupon')->find($value);

        if (!$riderCoupon) {
            $fail(trans_fallback('messages.coupon.not_exist', 'The selected coupon does not exist.'));
            return;
        }

        if ($riderCoupon->rider_id !== Auth::id()) {
            $fail(trans_fallback('messages.coupon.not_yours', 'This coupon does not belong to you.'));
            return;
        }

        $coupon = $riderCoupon->coupon;

        if (!$coupon->isActive()) {
            $fail(trans_fallback('messages.coupon.not_active', 'The selected coupon is not active.'));
        }

       
    }
}
