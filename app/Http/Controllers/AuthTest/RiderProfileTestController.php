<?php

namespace App\Http\Controllers\AuthTest;


use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rider\RiderCompleteProfileRequest;
use App\Http\Requests\UpdateAgentProfileRequest;
use App\Models\Agent;
use App\Services\AssetsService;
use Exception;

class RiderProfileTestController extends Controller
{
    // app/Http/Controllers/Api/Agent/ProfileController.php
    public function completeProfile(RiderCompleteProfileRequest $request)
    {
        /** @var Rider $rider */ // Add PHPDoc type hint
        $rider = auth('rider-api')->user();
        // Handle paperwork file upload
        try {
            $data = $request->validated();
            $rider->update(attributes: $data);
            return ApiResponse::sendResponseSuccess(
                $rider->fresh(),
                trans_fallback('messages.rider.completion_success', 'Rider Profile Completed')
            );
        } catch (Exception $e) {
            return ApiResponse::sendResponseError(
                trans_fallback('messages.error.creation_failed', 'Creation failed: ') . $e->getMessage()
            );
        }
    }
}
