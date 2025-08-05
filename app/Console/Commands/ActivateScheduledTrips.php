<?php

namespace App\Console\Commands;

use App\Enums\TripStatusEnum;
use App\Events\TripActivation;
use App\Models\Trip;
use Illuminate\Console\Command;

class ActivateScheduledTrips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trips:activate-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate scheduled trips that are due to start';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Activating scheduled trips...');

        $trips = Trip::where('trip_status_id', TripStatusEnum::DriverAssigned->value)
            ->whereNotNull('driver_id')
            ->where('requested_time', '<=', now()->addMinutes(15))
            ->get();

        foreach ($trips as $trip) {
            $this->info("Activating trip #{$trip->id}");
            event(new TripActivation($trip));
        }

        $this->info('Done.');

        return 0;
    }
}
