<?php

namespace App\Http\Controllers\Admin;

use App\Events\AccountSuspended;
use App\Http\Controllers\Controller;
use App\Http\Requests\Suspension\StoreSuspensionRequest;
use App\Models\Driver;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use App\Models\AccountSuspension;

class DriverSuspensionController extends Controller
{
    public function index(Driver $driver)
    {
        return $driver->suspensions;
    }

    public function store(StoreSuspensionRequest $request, Driver $driver): JsonResponse
    {
        $validated = $request->validated();
        
        $suspension=null;
        if (isset($validated['is_permanent']) && $validated['is_permanent']) {
            $suspension = $driver->suspendForever($validated['suspension_id']);
        } else {
            $suspension = $driver->suspendTemporarily($validated['suspension_id'], Date::parse($validated['suspended_until']));
        }

        if ($suspension) {
            event(new AccountSuspended($driver, $suspension));
        }

        return response()->json(['message' => 'Driver suspended successfully.']);
    }

    public function show(Driver $driver, AccountSuspension $suspension)
    {
        return $suspension;
    }

    public function destroy(Driver $driver, AccountSuspension $suspension): JsonResponse
    {
        $suspension->lift();

        return response()->json(['message' => 'Suspension lifted successfully.']);
    }
}
