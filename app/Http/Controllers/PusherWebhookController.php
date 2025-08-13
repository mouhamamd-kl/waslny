<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Services\DriverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PusherWebhookController extends Controller
{
    protected DriverService $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    public function handle(Request $request)
    {
        $events = $request->json('events', []);

        foreach ($events as $event) {
            if (in_array($event['name'], ['channel_vacated', 'member_removed'])) {
                $channelName = $event['channel'];
                if (strpos($channelName, 'presence-driver-') === 0) {
                    $driverId = str_replace('presence-driver-', '', $channelName);
                    $driver = Driver::find($driverId);
                    if ($driver) {
                        $this->driverService->updateLastActiveAt($driver);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
