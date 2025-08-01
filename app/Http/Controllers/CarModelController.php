<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CarModelRequest;
use App\Http\Requests\CarModelSearchRequest;
use App\Http\Resources\CarModelResource;
use App\Models\CarModel;
use App\Services\CarModelService;
use Exception;
use Illuminate\Http\Request;

class CarModelController extends Controller
{
    protected $carModelService;

    public function __construct(CarModelService $carModelService)
    {
        $this->carModelService = $carModelService;
    }
    /**
     * Display a listing of the resource.
     */
    public function adminIndex(Request $request)
    {
        try {
            $car_models = $this->carModelService->searchCarModel(
                $request->input('filters', []),
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $car_models,
                CarModelResource::class,
                trans_fallback('messages.car_model.list', 'Car Model retrieved successfully')
            );
        } catch (Exception $e) {
            throw $e;
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred')
            );
        }
    }

    public function index(Request $request)
    {
        try {
            $car_models = $this->carModelService->searchCarModel(
                $request->input('filters', ['is_active' => true]),
                $request->input('perPage', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $car_models,
                CarModelResource::class,
                trans_fallback('messages.car_model.list', 'Car Model retrieved successfully')
            );
        } catch (Exception $e) {
            throw $e;
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.generic', 'An error occurred')
            );
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function adminSearch(CarModelSearchRequest $request)
    {
        try {
            $filters = $request->validated();
            $car_models = $this->carModelService->searchCarModel(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $car_models,
                CarModelResource::class, // Add your resource class
                trans_fallback('messages.car_model.list', 'Car Model retrieved successfully'),
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
    public function search(CarModelSearchRequest $request)
    {
        try {
            $filters = $request->validated();
            $filters['is_active'] = true;
            $car_models = $this->carModelService->searchCarModel(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $car_models,
                CarModelResource::class, // Add your resource class
                trans_fallback('messages.car_model.list', 'Car Model retrieved successfully'),
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
    public function store(CarModelRequest $request)
    {
        try {
            $data = $request->validate();
            $car_model = $this->carModelService->create($data);
            return ApiResponse::sendResponseSuccess(
                $car_model,
                CarModelResource::class,
                trans_fallback('messages.car_model.created', 'Car Models retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'Car Model Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $car_model = $this->carModelService->findById($id);
            return ApiResponse::sendResponseSuccess(data: new CarModelResource($car_model), message: trans_fallback('messages.car_model.retrieved', 'Car Model Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Car Model not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CarModelRequest $request, string $id)
    {
        try {
            $car_model = $this->carModelService->update((int) $id, $request->validated());
            return ApiResponse::sendResponseSuccess(
                new CarModelResource($car_model),
                trans_fallback('messages.car_model.updated', 'Car Model updated successfully')
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
            $this->carModelService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.car_model.deleted', 'Car Model updated successfully')
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
            /** @var CarModel $car_model */ // Add PHPDoc type hint
            $car_model = $this->carModelService->findById($id);
            $car_model->deactivate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.car_model.deactivated', 'Car Model DeActivated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.car_model.error.deactivation_failed', 'DeActivation failed: ' . $e->getMessage())
            );
        }
    }

    public function activate($id)
    {
        try {
            /** @var CarModel $car_model */ // Add PHPDoc type hint
            $car_model = $this->carModelService->findById($id);
            $car_model->activate();
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.car_model.activated', 'Car Model Activated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.car_model.error.activation_failed', 'Activation failed: ' . $e->getMessage())
            );
        }
    }
}
