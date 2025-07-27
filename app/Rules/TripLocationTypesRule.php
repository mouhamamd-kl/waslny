<?php

namespace App\Rules;

use App\Enums\LocationTypeEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TripLocationTypesRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $typeCounts = array_count_values(
            array_column($value, 'location_type')
        );

        $hasOnePickup = isset($typeCounts[LocationTypeEnum::Pickup->value]) &&
            $typeCounts[LocationTypeEnum::Pickup->value] === 1;
        if (!$$hasOnePickup) {
            $fail(trans_fallback('validation.trip_location_types.pickup', 'Trip Location Must Have One PickUp'));
        }
        $hasOneDropoff = isset($typeCounts[LocationTypeEnum::DropOff->value]) &&
            $typeCounts[LocationTypeEnum::DropOff->value] === 1;
        if (!$hasOneDropoff) {
            $fail(trans_fallback('validation.trip_location_types.dropoff', 'Trip Location Must Have One DropOff'));
        }
    }
}
