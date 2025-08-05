<?php

namespace App\Console\Commands;

use App\Enums\TripStatusEnum;
use App\Events\TripCreated;
use App\Models\Trip;
use Illuminate\Console\Command;

class ProcessScheduledTrips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trips:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled trips that are due to start';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Processing scheduled trips...');

        $trips = Trip::whereHas('status', function ($query) {
            $query->where('name', TripStatusEnum::Pending->value);
        })
            ->where('requested_time', '<=', now()->addMinutes(15))
            ->get();

        foreach ($trips as $trip) {
            $this->info("Processing trip #{$trip->id}");
            $trip->update(['trip_status_id' => TripStatusEnum::Searching->value]);
            event(new TripCreated($trip));
        }

        $this->info('Done.');

        return 0;
    }
}
