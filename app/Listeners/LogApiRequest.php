<?php

// namespace App\Listeners;

// use App\Events\TestNotification;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Support\Facades\Log;

// class LogApiRequest
// {
//     /**
//      * Create the event listener.
//      */
//     public function __construct()
//     {
//         //
//     }

//     /**
//      * Handle the event.
//      */
//    public function handle(TestNotification $event): void
//     {
//         // Convert array to JSON for safe logging
//         $jsonData = json_encode($event->data, JSON_PRETTY_PRINT);
        
//         Log::info("API Request Event Triggered: $jsonData", [
//             'data' => $event->data,
//             'timestamp' => now()
//         ]);
//     }
// }
