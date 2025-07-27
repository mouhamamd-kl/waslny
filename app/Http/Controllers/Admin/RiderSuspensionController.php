<?php

namespace App\Http\Controllers\Admin;

use App\Events\AccountSuspended;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuspensionRequest;
use App\Models\Rider;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use App\Models\AccountSuspension;

class RiderSuspensionController extends Controller
{
    public function index(Rider $rider)
    {
        return $rider->suspensions;
    }

    public function store(StoreSuspensionRequest $request, Rider $rider): JsonResponse
    {
        $validated = $request->validated();

        if (isset($validated['is_permanent']) && $validated['is_permanent']) {
            $suspension = $rider->suspendForever($validated['suspension_id']);
        } else {
            $suspension = $rider->suspendTemporarily($validated['suspension_id'], Date::parse($validated['suspended_until']));
        }

        if ($suspension) {
            event(new AccountSuspended($rider, $suspension));
        }

        return response()->json(['message' => 'Rider suspended successfully.']);
    }

    public function show(Rider $rider, AccountSuspension $suspension)
    {
        return $suspension;
    }

    public function destroy(Rider $rider, AccountSuspension $suspension): JsonResponse
    {
        $suspension->lift();

        return response()->json(['message' => 'Suspension lifted successfully.']);
    }
}
