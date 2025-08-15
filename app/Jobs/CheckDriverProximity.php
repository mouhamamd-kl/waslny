<?php

namespace App\Jobs;

use App\Events\DriverApproachingPickup;
use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// class CheckDriverProximity implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     public $trip;

//     /**
//      * Create a new job instance.
//      *
//      * @return void
//      */
//     public function __construct(Trip $trip)
//     {
//         $this->trip = $trip;
//     }

//     /**
//      * Execute the job.
//      *
//      * @return void
//      */
//     public function handle()
//     {
//         // trip_flow
//         $pickupLocation = $this->trip->locations()->pickupPoints()->first();
//         $driverLocation = $this->trip->driver->location;

//         if ($pickupLocation && $driverLocation) {
//             $driverTripLocation = new \App\Models\TripLocation(['location' => $driverLocation]);
//             $distance = $pickupLocation->distanceTo($driverTripLocation);

//             // If driver is within 1km and we haven't already notified, fire the event
//             if ($distance <= 1000 && !$this->trip->approaching_pickup_notified_at) {
//                 $this->trip->update(['approaching_pickup_notified_at' => now()]);
//                 event(new \App\Events\DriverApproachingPickup($this->trip, $this->trip->driver));
//                 return; // Stop checking
//             }
//         }

//         // If the trip is still in progress, re-queue the job to check again in 30 seconds
//         if ($this->trip->driver_id && !$this->trip->getCompletedAtAttribute() && !$this->trip->approaching_pickup_notified_at) {
//             self::dispatch($this->trip)->delay(now()->addSeconds(30));
//         }
//     }
// }
