<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CarServiceLevel\CarServiceLevelSearchRequest;
use App\Http\Requests\CarServiceLevel\CarServiceLevelStoreRequest;
use App\Http\Requests\CarServiceLevel\CarServiceLevelUpdateRequest;
use App\Http\Resources\CarServiceLevelResource;
use App\Models\CarServiceLevel;
use App\Services\CarServiceLevelService;
use Exception;
use Illuminate\Http\Request;

class CarServiceLevelController extends Controller
{
    protected $carServiceLevelService;

    public function __construct(CarServiceLevelService $carServiceLevelService)
    {
        $this->carServiceLevelService = $carServiceLevelService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $car_service_levels = $this->carServiceLevelService->searchCarServiceLevel(
                $request->input('filters', []),
                $request->input('per_page', 10)
            );
            return ApiResponse::sendResponsePaginated(
                $car_service_levels,
                CarServiceLevelResource::class,
                trans_fallback('messages.car_service_level.list', 'Car Service Level retrieved successfully')
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
    public function search(CarServiceLevelSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn($value) => !is_null($value));
            $car_service_levels = $this->carServiceLevelService->searchCarServiceLevel(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $car_service_levels,
                CarServiceLevelResource::class, // Add your resource class
                trans_fallback('messages.car_service_level.list', 'Car Service Level retrieved successfully'),
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
    public function store(CarServiceLevelStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $car_service_level = $this->carServiceLevelService->create($data);
            return ApiResponse::sendResponseSuccess(
                new CarServiceLevelResource($car_service_level),
                trans_fallback('messages.car_service_level.created', 'Car Service Levels retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Car Service Level Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $car_service_level = $this->carServiceLevelService->findById($id);
            if (!$car_service_level) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Service Level not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new CarServiceLevelResource($car_service_level), message: trans_fallback('messages.car_service_level.retrieved', 'Car Service Level Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Service Level not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CarServiceLevelUpdateRequest $request, string $id)
    {
        try {
            $car_service_level = $this->carServiceLevelService->findById($id);
            if (!$car_service_level) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Service Level not found'), 404);
            }
            $data = array_filter($request->validated(), fn($value) => !is_null($value));
            $car_service_level = $this->carServiceLevelService->update((int) $id, $data);
            return ApiResponse::sendResponseSuccess(
                new CarServiceLevelResource($car_service_level),
                trans_fallback('messages.car_service_level.updated', 'Car Service Level updated successfully')
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
            $car_service_level = $this->carServiceLevelService->findById($id);
            if (!$car_service_level) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'CarServiceLevel not found'), 404);
            }

            if ($car_service_level->carModels()->exists() || $car_service_level->pricings()->exists()) {
                return ApiResponse::sendResponseError(
                    trans_fallback('messages.car_service_level.error.has_models_or_pricings', 'Cannot delete a car service level that has car models or pricings associated with it.'),
                    409
                );
            }

            $this->carServiceLevelService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.car_service_level.deleted', 'CarServiceLevel updated successfully')
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
            /** @var CarServiceLevel $CarServiceLevel */ // Add PHPDoc type hint
            $CarServiceLevel = $this->carServiceLevelService->findById($id);
            if (!$CarServiceLevel) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Service Level not found'), 404);
            }
            $CarServiceLevel->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.car_service_level.deactivated', 'Car Service Level DeActivated successfully')
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
            /** @var CarServiceLevel $car_model */ // Add PHPDoc type hint
            $CarServiceLevel = $this->carServiceLevelService->findById($id);
            if (!$CarServiceLevel) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Service Level not found'), 404);
            }
            $CarServiceLevel->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.car_service_level.activated', 'Car Service Level Activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.car_service_level.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
}
