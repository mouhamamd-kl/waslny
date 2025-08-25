<?php

namespace App\Console\Commands;

use App\Enums\TripTimeTypeEnum;
use App\Events\TripTimeIsNow;
use App\Events\TripTimeNear;
use App\Models\Trip;
use Illuminate\Console\Command;
use Carbon\Carbon;

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

        $this->handleTripsNear();
        $this->handleTripsNow();

        $this->info('Done.');

        return 0;
    }

    protected function handleTripsNear()
    {
        $trips = Trip::query()
            ->whereHas('status', fn($q) => $q->where('system_value', \App\Enums\TripStatusEnum::Scheduled->value))
            ->whereBetween('requested_time', [now(), now()->addMinutes(5)])
            ->get();

        $trips->each(fn(Trip $trip) => event(new TripTimeNear($trip)));

        $this->info("{$trips->count()} scheduled trips are near.");
    }

    protected function handleTripsNow()
    {
        $trips = Trip::query()
            ->whereHas('timeType', fn($q) => $q->where('system_value', TripTimeTypeEnum::SCHEDULED->value))
            ->whereHas('status', fn($q) => $q->where('system_value', \App\Enums\TripStatusEnum::Scheduled->value))
            ->where('requested_time', '<=', now())
            ->get();

        $trips->each(fn(Trip $trip) => event(new TripTimeIsNow($trip)));

        $this->info("{$trips->count()} scheduled trips are now.");
    }
}
