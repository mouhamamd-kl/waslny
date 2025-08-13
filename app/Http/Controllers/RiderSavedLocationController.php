<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\RiderSavedLocation\RiderSavedLocationRequest;
use App\Http\Resources\RiderSavedLocationResource;
use App\Models\RiderSavedLocation;
use App\Services\RiderSavedLocationService;
use Exception;
use Illuminate\Http\Request;

class RiderSavedLocationController extends Controller
{
    protected $rider_saved_location_service;

    public function __construct(RiderSavedLocationService $rider_saved_location_service)
    {
        $this->rider_saved_location_service = $rider_saved_location_service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->input('filters');
            $filters['rider_id'] = auth('rider-api')->user()->id;
            $rider_saved_locations = $this->rider_saved_location_service->searchRiderSavedLocations(
                $filters,
                $request->input('per_page', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $rider_saved_locations,
                RiderSavedLocationResource::class,
                trans_fallback('messages.rider_saved_location.list', 'Rider Location Folder retrieved successfully')
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
    // public function search(Request $request)
    // {
    //     try {
    //         $filters = $request->only([
    //             'name',
    //             'is_active',
    //         ]);
    //         $rider_saved_locations = $this->rider_saved_location_service->searchRiderFolders(
    //             filters: $filters,
    //             perPage: $request->input('per_page', 10),
    //         );

    //         return ApiResponse::sendResponsePaginated(
    //             $rider_saved_locations,
    //             RiderSavedLocationResource::class, // Add your resource class
    //             trans_fallback('messages.rider_saved_location.list', 'Rider Location Folder retrieved successfully'),
    //         );
    //     } catch (Exception $e) {
    //         return ApiResponse::sendResponseError(
    //             'Search failed: ' . $e->getMessage(),
    //             500
    //         );
    //     }
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function store(RiderSavedLocationRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['rider_id'] = $request->user()->id;
            $rider_saved_location = $this->rider_saved_location_service->create($validatedData);
            return ApiResponse::sendResponseSuccess(
                new RiderSavedLocationResource($rider_saved_location),
                trans_fallback('messages.rider_saved_location.created', 'Rider Location Folder retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'rider_saved_location Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $rider_saved_location = $this->rider_saved_location_service->findById($id);
            if (!$rider_saved_location) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider Saved Location not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new RiderSavedLocationResource($rider_saved_location), message: trans_fallback('messages.rider_saved_location.retrieved', 'rider_saved_location Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider Saved Location not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RiderSavedLocationRequest $request, string $id)
    {
        try {
            $rider_saved_location = $this->rider_saved_location_service->findById($id);
            if (!$rider_saved_location) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider Saved Location not found'), 404);
            }
            $data = array_filter($request->validated(), fn($value) => !is_null($value));
            $rider_saved_location = $this->rider_saved_location_service->update((int) $id, $data);
            return ApiResponse::sendResponseSuccess(
                new RiderSavedLocationResource($rider_saved_location),
                trans_fallback('messages.rider_saved_location.updated', 'Rider Saved Location updated successfully')
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
            $rider_saved_location = $this->rider_saved_location_service->findById($id);
            if (!$rider_saved_location) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'Rider Saved Location not found'), 404);
            }
            $this->rider_saved_location_service->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.rider_saved_location.deleted', 'Rider Saved Location updated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.delete_failed', 'Delete failed: ' . $e->getMessage())
            );
        }
    }
}
