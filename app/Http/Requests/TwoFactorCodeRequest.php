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

class TwoFactorCodeRequest extends BaseRequest
{

    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
    // public function authorize(): bool
    // {
    //     return true;
    //     // return auth('rider-api')->check();
    // }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function handleAuthorization(): bool
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
            'phone' => 'required',
            'otp' => 'required|digits:6',
        ];
    }
}
