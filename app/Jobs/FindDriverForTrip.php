<?php

namespace App\Jobs;

use App\Events\DriverAssigned;
use App\Events\TripUnavailable;
use App\Events\DriverPreAssigned;
use App\Enums\TripStatusEnum;
use App\Enums\TripTimeTypeEnum;
use App\Events\SearchTimeout;
use App\Models\Trip;
use App\Services\TripService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FindDriverForTrip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $trip;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TripService $tripService)
    {
        // trip_flow
        if ($this->trip->driver_id) {
            return;
        }

        if ($this->trip->search_expires_at->isPast()) {
            $tripService->update($this->trip->id, ['trip_status_id' => TripStatusEnum::SystemCancelled->value]);
            event(new SearchTimeout($this->trip));
            return;
        }

        $driver = $tripService->findAndAssignDriver($this->trip);

        if ($driver) {
            if ($this->trip->trip_time_type_id === TripTimeTypeEnum::INSTANT->value) {
                event(new DriverAssigned($this->trip, $driver));
                $otherDrivers = $tripService->getDriversNotAccepting($this->trip, $driver);
                if ($otherDrivers->isNotEmpty()) {
                    event(new TripUnavailable($this->trip, $otherDrivers));
                }
            } 
        } else {
            self::dispatch($this->trip)->delay(now()->addSeconds(1));
        }
    }
}
