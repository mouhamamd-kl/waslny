<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\CountrySearchRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Services\CountryService;
use Exception;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $countries = $this->countryService->searchCountries(
                $request->input('filters', []),
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $countries,
                CountryResource::class,
                trans_fallback('messages.country.list', 'Countries retrieved successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred')
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function search(CountrySearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
            $countries = $this->countryService->searchCountries(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $countries,
                CountryResource::class, // Add your resource class
                trans_fallback('messages.country.list', 'Countries retrieved successfully'),
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                'Search failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(CountryRequest $request)
    {
        try {
            $data = $request->validated();
            $country = $this->countryService->create($data);
            return ApiResponse::sendResponseSuccess(
                new CountryResource($country),
                trans_fallback('messages.country.created', 'Country created successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Country Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $country = $this->countryService->findById($id);
            if (!$country) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Country not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new CountryResource($country), message: trans_fallback('messages.country.retrieved', 'Country Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Country not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryRequest $request, string $id)
    {
        try {
            $country = $this->countryService->findById($id);
            if (!$country) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Country not found'), 404);
            }
            $country = $this->countryService->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new CountryResource($country),
                trans_fallback('messages.country.updated', 'Country updated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.update_failed', 'Update failed: ' . $e->getMessage())
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $country = $this->countryService->findById($id);
            if (!$country) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Country not found'), 404);
            }
            $this->countryService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.country.deleted', 'Country updated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.delete_failed', 'Delete failed: ' . $e->getMessage())
            );
        }
    }

    public function deActivate($id)
    {
        try {
            /** @var Country $country */ // Add PHPDoc type hint
            $country = $this->countryService->findById($id);
            if (!$country) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Country not found'), 404);
            }
            $country->deactivate();
            return ApiResponse::sendResponseSuccess(
                null,
                trans_fallback('messages.country.deactivated', 'Country deactivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.car_service_level.error.deactivation_failed', 'DeActivation failed: ' . $e->getMessage())
            );
        }
    }

    public function activate($id)
    {
        try {
            /** @var Country $country */ // Add PHPDoc type hint
            $country = $this->countryService->findById($id);
            if (!$country) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Country not found'), 404);
            }
            $country->activate();
            return ApiResponse::sendResponseSuccess(
                null,
                trans_fallback('messages.country.activated', 'Country activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.car_service_level.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
}
