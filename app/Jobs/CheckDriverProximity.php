<?php

namespace App\Jobs;

use App\Events\DriverApproachingPickup;
use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Clickbar\Magellan\Database\PostgisFunctions\ST;

class CheckDriverProximity implements ShouldQueue
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
    public function handle(): void
    {
        if ($this->shouldStop()) {
            return;
        }

        $pickupLocation = $this->trip->pickup_location;
        $driverLocation = $this->trip->driver->location;

        if ($pickupLocation && $driverLocation) {
            $distance = DB::query()
                ->select(ST::distanceSphere($pickupLocation->location, $driverLocation)->as('distance'))
                ->first()
                ->distance;

            if ($distance <= 1000) {
                $this->trip->update(['approaching_pickup_notified_at' => now()]);
                event(new DriverApproachingPickup($this->trip, $this->trip->driver));
                return;
            }
        }

        $this->tries++;
        $this->release(now()->addSeconds($this->backoff()));
    }

    private function shouldStop(): bool
    {
        return !$this->trip->driver_id
            || $this->trip->getCompletedAtAttribute()
            || $this->trip->getCancelledAtAttribute()
            || $this->trip->approaching_pickup_notified_at;
    }

    public function backoff(): int
    {
        return (int) pow(2, $this->tries);
    }
}
