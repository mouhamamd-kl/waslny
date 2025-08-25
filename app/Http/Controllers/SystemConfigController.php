<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\SystemConfig\UpdateRequest;
use App\Http\Resources\SystemConfigResource;
use App\Services\SystemConfigService;
use Exception;
use Illuminate\Http\Request;

class SystemConfigController extends Controller
{
    protected $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $configs = $this->systemConfigService->searchSystemConfigs(
                $request->input('filters', []),
                $request->input('per_page', 10)
            );
            return ApiResponse::sendResponsePaginated(
                $configs,
                SystemConfigResource::class,
                trans_fallback('messages.system_config.list', 'System configs retrieved successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.generic', 'An error occurred'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        try {
            $config = $this->systemConfigService->findById($id);
            if (!$config) {
                return ApiResponse::sendResponseError(trans_fallback('messages.error.not_found', 'System config not found'), 404);
            }
            $data = $request->validated();
            $config = $this->systemConfigService->update($id, $data);
            return ApiResponse::sendResponseSuccess(
                new SystemConfigResource($config),
                trans_fallback('messages.system_config.updated', 'System config updated successfully')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(trans_fallback('messages.error.update_failed', 'Update failed'));
        }
    }
}
