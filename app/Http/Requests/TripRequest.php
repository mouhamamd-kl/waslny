<?php

namespace App\Http\Requests;

use App\Enums\LocationTypeEnum;
use App\Rules\ActiveCoupon;
use Carbon\Carbon;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Http\Requests\TransformsGeojsonGeometry;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use App\Rules\JsonValidatorRule;
use App\Rules\TripLocationOrderRule;
use App\Rules\TripLocationTypesRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TripRequest extends BaseRequest
{
    use TransformsGeojsonGeometry;
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
        return [
            'trip_type_id' => [
                $this->isRequired(),
                'exists:trip_types,id'
            ],

            'trip_time_type_id' => [
                $this->isRequired(),
                'exists:trip_time_types,id'
            ],

            'coupon_id' => [
                'sometimes',
                'exists:coupons,id',
                new ActiveCoupon,
            ],

            'payment_method_id' => [
                $this->isRequired(),
                'exists:payment_methods,id'
            ],

            // Date/time validation
            'requested_time' => [
                'required',
                function ($attribute, $value, $fail) {
                    $datetime = Carbon::createFromFormat('Y-m-d H:i', $value);

                    if (!$datetime) {
                        $fail('Invalid datetime format. Use YYYY-MM-DD HH:MM.');
                    }

                    if ($datetime->isPast()) {
                        $fail('The start time must be in the future.');
                    }
                },
            ],

            //Trip Locations Validation
            'locations' => ['array', 'min:2', 'required', 'filled'],
            'locations.*' => [
                new JsonValidatorRule(['location', 'location_order', 'location_type']),
                new TripLocationTypesRule,
                new TripLocationOrderRule,
            ],
            'locations.*.location' => [new GeometryGeojsonRule([Point::class]),],
            'locations.*.location_order' => ['integer', 'min:1', 'distinct'],
            'locations.*.location_type' => [Rule::enum(LocationTypeEnum::class)],

            // 'locations.*.estimated_arrival_time' => [
            //     'required',
            //     function ($attribute, $value, $fail) {
            //         $datetime = Carbon::createFromFormat('Y-m-d H:i', $value);

            //         if (!$datetime) {
            //             $fail('Invalid datetime format. Use YYYY-MM-DD HH:MM.');
            //         }

            //         if ($datetime->isPast()) {
            //             $fail('The arrival time must be in the future.');
            //         }
            //     },
            // ],
        ];
    }
    public function geometries(): array
    {
        return ['locations.*.location'];
    }
}
