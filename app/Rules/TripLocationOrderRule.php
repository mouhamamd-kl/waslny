<?php

namespace App\Rules;

use App\Enums\LocationTypeEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TripLocationOrderRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sorted = collect($value)->sortBy('location_order');

        $firstType = $sorted->first()['location_type'] ?? null;
        $lastType = $sorted->last()['location_type'] ?? null;

        if ($firstType === LocationTypeEnum::Pickup->value) {
            $fail(trans_fallback('validation.trip_location_order.pickup_first', 'First Trip Location Must Be PickUp'));
        }
        if ($lastType === LocationTypeEnum::DropOff->value) {
            $fail(trans_fallback('validation.trip_location_order.dropoff_last', 'Last Trip Location Must Be DropOff'));
        }
    }
}
