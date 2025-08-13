<?php

namespace App\Observers;

use App\Models\Driver;
use App\Services\DriverService;

class DriverObserver
{
    protected DriverService $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Handle the Driver "created" event.
     */
    public function created(Driver $driver): void
    {
        //
    }

    /**
     * Handle the Driver "updated" event.
     */
    public function updated(Driver $driver): void
    {
        if ($driver->isDirty('driver_status_id')) {
            $this->driverService->updateLastActiveAt($driver);
        }
    }

    /**
     * Handle the Driver "deleted" event.
     */
    public function deleted(Driver $driver): void
    {
        //
    }

    /**
     * Handle the Driver "restored" event.
     */
    public function restored(Driver $driver): void
    {
        //
    }

    /**
     * Handle the Driver "force deleted" event.
     */
    public function forceDeleted(Driver $driver): void
    {
        //
    }
}
