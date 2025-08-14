<?php

namespace App\Http\Requests\Trip;

use App\Enums\LocationTypeEnum;
use App\Http\Requests\BaseRequest;
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

class TripFineRequest extends BaseRequest
{
    use TransformsGeojsonGeometry;

    public function handleAuthorization(): bool
    {
        return auth('rider-api')->check();
    }

    public function rules(): array
    {
        return [
            'car_service_level_id' => ['required', 'exists:car_service_levels,id'],
            'locations' => ['array', 'min:2', 'required', 'filled'],
            'locations' => [new TripLocationTypesRule],
            'locations.*' => [
                new JsonValidatorRule(['location', 'location_order', 'location_type']),
                new TripLocationOrderRule,
            ],
            'locations.*.location' => [new GeometryGeojsonRule([Point::class]),],
            'locations.*.location_order' => ['integer', 'min:1', 'distinct'],
            'locations.*.location_type' => [Rule::enum(LocationTypeEnum::class)],
        ];
    }
    public function geometries(): array
    {
        return ['locations.*.location'];
    }
}
