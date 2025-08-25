<?php

namespace App\Jobs;

use App\Events\DriverAssigned;
use App\Events\TripUnavailable;
use App\Enums\TripStatusEnum;
use App\Enums\TripTimeTypeEnum;
use App\Events\SearchTimeout;
use App\Events\TestNotification;
use App\Models\Trip;
use App\Services\TripService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FindDriverForTrip implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Trip $trip;
    public int $tries;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Trip $trip, int $tries = 0)
    {
        $this->trip = $trip;
        $this->tries = $tries;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TripService $tripService)
    {
        // The old implementation could work with stale data and had a potential null pointer exception.
        // event(new TestNotification(['message' => "FindDriverForTrip job started for trip ID: {$this->trip->id}"]));
        // if (!$this->trip) {
        //     Log::info("Trip Not Found");
        //     return;
        // }

        // New implementation: Refresh the trip model instance to ensure we have the latest data and handle null case.
        if ($this->trip) {
            $this->trip = $this->trip->fresh();
        }

        if (!$this->trip) {
            Log::info("Trip not found or was deleted.");
            return;
        }

        event(new TestNotification(['message' => "FindDriverForTrip job started for trip ID: {$this->trip->id}"]));
        if ($this->trip->status->system_value !== TripStatusEnum::Searching->value) {
            Log::info("Trip ID: {$this->trip->id} has already timed out. Exiting job.");
            return;
        }
        Log::info("FindDriverForTrip job started for trip ID: {$this->trip->id}");

        if ($this->trip->driver_id != null) {
            Log::info("Trip ID: {$this->trip->id} already has a driver. Exiting job.");
            return;
        }

        if ($this->trip->search_expires_at->isPast()) {
            Log::info("Search expired for trip ID: {$this->trip->id}.");
            $this->trip->transitionTo(TripStatusEnum::SearchTimeout);
            event(new SearchTimeout($this->trip));
            return;
        }

        Log::info("Searching for driver for trip ID: {$this->trip->id}.");
        // $driver = $tripService->findAndAssignDriver($this->trip);
        $drivers = $tripService->findAndNotifyDrivers($this->trip);

        // if ($driver) {
        if ($this->trip->fresh()->driver) {
            Log::info("Driver found for trip ID: {$this->trip->id}. Driver ID: {$this->trip->driver_id}");
            event(new DriverAssigned($this->trip, $this->trip->driver));
            $otherDrivers = $tripService->getDriversNotAccepting($this->trip, $this->trip->driver);
            if ($otherDrivers->isNotEmpty()) {
                event(new TripUnavailable($this->trip, $otherDrivers));
            }
        } else {
            if ($this->trip->search_expires_at->isPast()) {
                Log::info("Search expired for trip ID: {$this->trip->id}.");
                $this->trip->transitionTo(TripStatusEnum::SearchTimeout);
                event(new SearchTimeout($this->trip));
                return;
            }

            event(new TestNotification(['message' => "No driver found for trip ID: {$this->trip->id}. Re-dispatching job."]));
            Log::info("No drivers found for trip ID: {$this->trip->id}. Expanding search radius.");
            $this->trip->increment('driver_search_radius', 1000); // Expand by 1km
            Log::info("No driver found for trip ID: {$this->trip->id}. Re-dispatching job.");
            $this->tries++;
            $this->release(now()->addSeconds($this->backoff()));
        }
    }

    public function backoff(): int
    {
        return (int) pow(2, $this->tries);
    }

    public function uniqueId(): string
    {
        return $this->trip->id;
    }
}
