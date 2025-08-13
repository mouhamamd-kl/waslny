<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\RiderFolder\RiderFolderRequest;
use App\Http\Requests\RiderFolder\RiderFolderSearchRequest;
use App\Http\Resources\RiderFolderResource;
use App\Models\RiderFolder;
use App\Services\RiderFolderService;
use Exception;
use Illuminate\Http\Request;

class RiderFolderController extends Controller
{
    protected $riderFolderService;

    public function __construct(RiderFolderService $riderFolderService)
    {
        $this->riderFolderService = $riderFolderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters['rider_id'] = auth('rider-api')->user()->id;
            $rider_folders = $this->riderFolderService->searchRiderFolders(
                $filters,
                $request->input('per_page', 5)
            );
            return ApiResponse::sendResponsePaginated(
                $rider_folders,
                RiderFolderResource::class,
                trans_fallback('messages.rider_location_folder.list', 'Rider Location Folder retrieved successfully')
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
    public function search(RiderFolderSearchRequest $request)
    {
        try {
            $filters = array_filter($request->validated(), fn ($value) => !is_null($value));
            $filters['rider_id'] = auth('rider-api')->user()->id;
            $rider_folders = $this->riderFolderService->searchRiderFolders(
                filters: $filters,
                perPage: $request->input('per_page', 10),
            );

            return ApiResponse::sendResponsePaginated(
                $rider_folders,
                RiderFolderResource::class, // Add your resource class
                trans_fallback('messages.rider_location_folder.list', 'Rider Location Folder retrieved successfully'),
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
    public function store(RiderFolderRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['rider_id'] = $request->user()->id;
            $rider_folder = $this->riderFolderService->create($validatedData);
            return ApiResponse::sendResponseSuccess(
                new RiderFolderResource($rider_folder),
                trans_fallback('messages.rider_location_folder.created', 'Rider Location Folder retrieved successfully'),
                201
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.creation_failed', 'rider_location_folder Creation Failed') . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $rider_folder = $this->riderFolderService->findById($id);
            if (!$rider_folder) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'rider_location_folder not found'), 404);
            }
            return ApiResponse::sendResponseSuccess(data: new RiderFolderResource($rider_folder), message: trans_fallback('messages.rider_location_folder.retrieved', 'rider_location_folder Retrived Successfully'));
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'rider_location_folder not found'), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RiderFolderRequest $request, string $id)
    {
        try {
            $rider_folder = $this->riderFolderService->findById($id);
            if (!$rider_folder) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'rider_location_folder not found'), 404);
            }
            $data = array_filter($request->validated(), fn($value) => !is_null($value));
            $rider_folder = $this->riderFolderService->update((int) $id, $data);
            return ApiResponse::sendResponseSuccess(
                new RiderFolderResource($rider_folder),
                trans_fallback('messages.rider_location_folder.updated', 'rider_location_folder updated successfully')
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
            $rider_folder = $this->riderFolderService->findById($id);
            if (!$rider_folder) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'rider_location_folder not found'), 404);
            }
            $this->riderFolderService->delete((int) $id);
            return ApiResponse::sendResponseSuccess(
                message: trans_fallback('messages.rider_location_folder.deleted', 'rider_location_folder updated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.delete_failed', 'Delete failed: ' . $e->getMessage())
            );
        }
    }
}
