<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CarManufacturerRequest;
use App\Http\Requests\CarManufacturerSearchRequest;
use App\Http\Resources\CarManufacturerResource;
use App\Models\CarManufacturer;
use App\Services\CarManufactureService;
use Exception;
use Illuminate\Http\Request;

class CarManufacturerController extends Controller
{
    protected $carManufactureService;

    public function __construct(CarManufactureService $carManufactureService)
    {
        $this->carManufactureService = $carManufactureService;
    }

    /**
     * Display a listing of the resource.
     */
    public function adminIndex(Request $request)
    {
        try {
            $car_manufactures = $this->carManufactureService->searchCarManufacture(
                filters: $request->input('filters', []),
                perPage: $request->input('perPage', 10000)
            );
            return ApiResponse::sendResponsePaginated(
                $car_manufactures,
                CarManufacturerResource::class,
                trans_fallback('messages.car_manufacture.list', 'Car Manufacture retrieved successfully')
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
    public function index(Request $request)
    {
        try {
            $car_manufactures = $this->carManufactureService->searchCarManufacture(
                filters: $request->input('filters', ['is_active' => true]),
                perPage: $request->input('perPage', 10000)
            );
            return ApiResponse::sendResponsePaginated(
                $car_manufactures,
                CarManufacturerResource::class,
                trans_fallback('messages.car_manufacture.list', 'Car Manufacture retrieved successfully')
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
    public function adminSearch(CarManufacturerSearchRequest $request)
    {
        try {
            $filters = $request->validated();
            $car_manufactures = $this->carManufactureService->searchCarManufacture(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $car_manufactures,
                CarManufacturerResource::class, // Add your resource class
                trans_fallback('messages.car_manufacture.list', 'Car Manufacture retrieved successfully'),
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                'Search failed: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function search(CarManufacturerSearchRequest $request)
    {
        try {
            $filters = $request->validated();
            $filters['is_active'] = true;
            $car_manufactures = $this->carManufactureService->searchCarManufacture(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $car_manufactures,
                CarManufacturerResource::class, // Add your resource class
                trans_fallback('messages.car_manufacture.list', 'Car Manufacture retrieved successfully'),
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
    public function store(CarManufacturerRequest $request)
    {
        try {
            $data = $request->validate();
            $car_manufacture = $this->carManufactureService->create($data);
            return ApiResponse::sendResponseSuccess(
                $car_manufacture,
                CarManufacturerResource::class,
                trans_fallback('messages.car_manufacture.created', 'Car Manufactures retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Car Manufacture Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $car_manufacture = $this->carManufactureService->findById($id);
            return ApiResponse::sendResponseSuccess(data: new CarManufacturerResource($car_manufacture), message: trans_fallback('messages.car_manufacture.retrieved', 'Car Manufacture Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Manufacture not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CarManufacturerRequest $request, string $id)
    {
        try {
            $car_manufacture = $this->carManufactureService->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new CarManufacturerResource($car_manufacture),
                trans_fallback('messages.car_manufacture.updated', 'Car Manufacture updated successfully')
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
            $this->carManufactureService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.car_manufacture.deleted', 'Car Manufacture updated successfully')
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
            /** @var CarManufacturer $car_manufacture */ // Add PHPDoc type hint
            $car_manufacture = $this->carManufactureService->findById($id);
            $car_manufacture->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.car_manufacture.deactivated', 'Car Manufacture DeActivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.car_manufacture.error.deactivation_failed', 'DeActivation failed: ' . $e->getMessage())
            );
        }
    }

    public function activate($id)
    {
        try {
            /** @var CarManufacturer $car_manufacture */ // Add PHPDoc type hint
            $car_manufacture = $this->carManufactureService->findById($id);
            $car_manufacture->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.car_manufacture.activated', 'Car Manufacture Activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.car_manufacture.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
}
