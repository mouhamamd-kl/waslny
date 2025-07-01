<?php

namespace App\Http\Requests;

use App\Enums\LocationTypeEnum;
use App\Rules\ActiveCoupon;
use Carbon\Carbon;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Http\Requests\TransformsGeojsonGeometry;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use App\Rules\JsonValidatorRule;

class TripRequest extends BaseRequest
{
    use TransformsGeojsonGeometry;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return auth('rider-api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Required ID's fields with existence checks
            'rider_id' => [
                $this->isRequired(),
                'exists:riders,id'
            ],

            'driver_id' => [
                $this->isRequired(),
                'exists:drivers,id'
            ],

            'trip_type_id' => [
                $this->isRequired(),
                'exists:trip_types,id'
            ],

            'trip_time_type_id' => [
                $this->isRequired(),
                'exists:trip_time_types,id'
            ],

            'coupon_id' => [
                $this->isRequired(),
                'exists:coupons,id',
                new ActiveCoupon,
            ],

            'payment_method_id' => [
                $this->isRequired(),
                'exists:payment_methods,id'
            ],

            // Date/time validation
            'start_time' => [
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
                new JsonValidatorRule(['location', 'location_order', 'location_type'])
            ],
            'locations.*.location' => [new GeometryGeojsonRule([Point::class]),],
            'locations.*.location_order' => 'integer|min:1',
            'locations.*.location_type' => [LocationTypeEnum::rule()],

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
