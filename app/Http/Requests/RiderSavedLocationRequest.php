<?php

namespace App\Http\Requests;

use App\Models\CarManufacturer;
use App\Models\Country;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Http\Requests\TransformsGeojsonGeometry;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiderSavedLocationRequest extends BaseRequest
{
    use TransformsGeojsonGeometry;
    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
    // public function authorize(): bool
    // {
    //     return true;
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
            'rider_folder_id' => [
                $this->isRequired(),
                'exists:rider_folders,id',
            ],
            'location' => ['required', new GeometryGeojsonRule([Point::class])],
        ];
    }
    public function geometries(): array
    {
        return ['location'];
    }
}
