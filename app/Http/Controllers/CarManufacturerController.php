<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CarManufacturer\CarManufacturerAdminSearchRequest;
use App\Http\Requests\CarManufacturer\CarManufacturerStoreRequest;
use App\Http\Requests\CarManufacturer\CarManufacturerUpdateRequest;
use App\Http\Requests\CarManufacturer\CarManufacturerDriverSearchRequest;
use App\Http\Requests\CarManufacturer\CarManufacturerSearchRequest;
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
            $filters = [];
            if (!auth('admin-api')) {
                $filters['is_active'] = true;
            }
            $car_manufactures = $this->carManufactureService->searchCarManufacture(
                filters: $request->input('filters', $filters),
                perPage: $request->input('per_page', 10)
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
    public function riderIndex(Request $request)
    {
        try {
            $filters = [];
            if (!auth('admin-api')) {
                $filters['is_active'] = true;
            }
            $car_manufactures = $this->carManufactureService->searchCarManufacture(
                filters: $request->input('filters', $filters),
                perPage: $request->input('per_page', 10)
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

    // /**
    //  * Display a listing of the resource.
    //  */
    // public function search(CarManufacturerSearchRequest $request)
    // {
    //     try {
    //         $filters = array_filter($request->validated(), fn($value) => !is_null($value));
    //         if (!auth('admin-api')) {
    //             $filters['is_active'] = true;
    //         }
    //         $car_manufactures = $this->carManufactureService->searchCarManufacture(
    //             filters: $filters,
    //             perPage: $request->input('per_page', 10),
    //         );

    //         return ApiResponse::sendResponsePaginated(
    //             $car_manufactures,
    //             CarManufacturerResource::class, // Add your resource class
    //             trans_fallback('messages.car_manufacture.list', 'Car Manufacture retrieved successfully'),
    //         );
    //     } catch (Exception $e) {
    //         return ApiResponse::sendResponseError(
    //             'Search failed: ' . $e->getMessage(),
    //             500
    //         );
    //     }
    // }

    /**
     * Display a listing of the resource.
     */
    public function adminSearch(CarManufacturerAdminSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
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
    public function driverSearch(CarManufacturerDriverSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
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
    public function store(CarManufacturerStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $car_manufacture = $this->carManufactureService->create($data);
            return ApiResponse::sendResponseSuccess(
                new CarManufacturerResource($car_manufacture),
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
            if (!$car_manufacture) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Manufacture not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new CarManufacturerResource($car_manufacture), message: trans_fallback('messages.car_manufacture.retrieved', 'Car Manufacture Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Manufacture not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CarManufacturerUpdateRequest $request, string $id)
    {
        try {
            $car_manufacture = $this->carManufactureService->findById($id);
            if (!$car_manufacture) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Manufacture not found'), 404);
            }
            $data = array_filter($request->validated(), fn($value) => !is_null($value));
            $car_manufacture = $this->carManufactureService->update((int) $id, $data);
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
            $car_manufacture = $this->carManufactureService->findById($id);
            if (!$car_manufacture) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Manufacture not found'), 404);
            }
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
            if (!$car_manufacture) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Manufacture not found'), 404);
            }
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
            if (!$car_manufacture) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Manufacture not found'), 404);
            }
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
