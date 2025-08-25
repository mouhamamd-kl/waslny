<?php

namespace App\Http\Requests\Trip;

use App\Enums\LocationTypeEnum;
use App\Http\Requests\BaseRequest;
use App\Rules\ActiveCoupon;
use App\Rules\ValidRiderCoupon;
use Carbon\Carbon;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Http\Requests\TransformsGeojsonGeometry;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use App\Rules\JsonValidatorRule;
use App\Rules\TripLocationOrderRule;
use App\Rules\TripLocationTypesRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TripStoreRequest extends BaseRequest
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
                'required',
                'exists:trip_types,id'
            ],

            'trip_time_type_id' => [
                'required',
                'exists:trip_time_types,id'
            ],
            'car_service_level_id' => ['required', 'exists:car_service_levels,id',],
            
            'rider_coupon_id' => [
                'nullable',
                'sometimes',
                new ValidRiderCoupon,
            ],

            'payment_method_id' => [
                'required',
                'exists:payment_methods,id'
            ],


            // Old validation rules for locations
            'locations' => ['array', 'min:2', 'required', 'filled'],
            'locations' => [new TripLocationTypesRule],

            // Combined and corrected validation rules for locations
            // 'locations' => ['required', 'array', 'min:2', 'filled', new TripLocationTypesRule],

            'locations.*' => [
                new JsonValidatorRule(['location', 'location_order', 'location_type']),
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

    protected function handleRequestPreparation(): void
    {
        if ($this->has('rider_coupon_id')) {
            $riderCoupon = \App\Models\RiderCoupon::find($this->input('rider_coupon_id'));
            if ($riderCoupon) {
                $this->merge(['coupon_id' => $riderCoupon->coupon_id]);
                $this->request->remove('rider_coupon_id');
            }
        }
    }
}
