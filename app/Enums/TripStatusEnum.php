<?php

namespace App\Enums;

enum TripStatusEnum: string
{
    // Initial states
    case Pending = 'pending';           // Awaiting driver assignment
    case Searching = 'searching';       // Actively looking for driver
    case Scheduled = 'scheduled';       // Confirmed with a driver for a future time

        // Driver assignment flow
    case DriverAssigned = 'driver_assigned';  // Driver accepted trip
    case DriverEnRoute = 'driver_en_route';   // Driver heading to pickup
    case DriverArrived = 'driver_arrived';     // Driver at pickup location

        // Trip progression
    case OnGoing = 'on_going';          // Trip in progress
    case Waiting = 'waiting';            // Temporary stop (e.g., multiple dropoffs)

        // Completion states
    case Completed = 'completed';        // Successful completion
    case PaymentPending = 'payment_pending'; // Awaiting payment processing

        // Cancellation states
    case RiderCancelled = 'rider_cancelled';
    case DriverCancelled = 'driver_cancelled';
    case SystemCancelled = 'system_cancelled'; // Auto-cancellation (timeout, fraud)

        // Special cases
    case EmergencyStopped = 'emergency_stopped';
    case Disputed = 'disputed';          // Post-trip issues
    case SearchTimeout = 'search_timeout';
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    public function canTransitionTo(TripStatusEnum $newStatus): bool
    {
        return match ($this) {
            // Initial states
            self::Pending => in_array($newStatus, [
                self::Searching,         // When schedule time arrives
                self::RiderCancelled     // Rider cancels before search starts
            ]),

            self::Scheduled => in_array($newStatus, [
                self::DriverEnRoute,
                self::RiderCancelled,
                self::DriverCancelled
            ]),

            // Driver search phase
            self::Searching => in_array($newStatus, [
                self::DriverAssigned,    // Driver accepts
                self::SystemCancelled,    // No drivers found/timeout
                self::RiderCancelled,
                self::SearchTimeout     // Rider cancels 
            ]),

            // Driver assignment
            self::DriverAssigned => in_array($newStatus, [
                self::DriverEnRoute,     // Driver starts moving to pickup
                self::DriverCancelled,   // Driver cancels after accepting
                self::RiderCancelled     // Rider cancels after assignment
            ]),

            // Driver moving to pickup
            self::DriverEnRoute => in_array($newStatus, [
                self::DriverArrived,     // Driver reaches pickup location
                self::DriverCancelled,   // Driver cancels en route
                self::RiderCancelled     // Rider cancels while driver is coming
            ]),

            // Pickup arrival
            self::DriverArrived => in_array($newStatus, [
                self::OnGoing,           // Trip starts
                self::RiderCancelled,     // Rider doesn't show up
                self::DriverCancelled,   // Driver cancels en route
            ]),

            // Active trip
            self::OnGoing => in_array($newStatus, [
                self::Waiting,          // Intermediate stop
                self::Completed,         // Reaches final destination
                self::EmergencyStopped,   // Safety incident
                self::RiderCancelled     // Rider cancels before search starts
            ]),

            // Intermediate stops
            self::Waiting => in_array($newStatus, [
                self::OnGoing,           // Continue trip
                self::EmergencyStopped,
                self::DriverCancelled,   // Driver cancels en route
                self::RiderCancelled     // Rider cancels while driver is coming
            ]),

            // Trip completion
            self::Completed => in_array($newStatus, [
                self::PaymentPending,    // Payment processing starts
                self::Disputed           // Rider/driver disputes trip
            ]),

            // Payment processing
            self::PaymentPending => in_array($newStatus, [
                self::Completed,         // Payment succeeded
                self::Disputed           // Payment failed/dispute
            ]),

            // Terminal states - no further transitions
            self::RiderCancelled,
            self::DriverCancelled,
            self::SystemCancelled,
            self::EmergencyStopped,
            self::Disputed,
            self::SearchTimeout => false,

            // Default case
            default => throw new \InvalidArgumentException("Unhandled status: {$this->value}")
        };
    }

    public function isActive(): bool
    {
        return match ($this) {
            self::Searching,
            self::DriverAssigned,
            self::DriverEnRoute,
            self::DriverArrived,
            self::OnGoing,
            self::Waiting => true,
            default => false
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::RiderCancelled,
            self::DriverCancelled,
            self::SystemCancelled,
            self::EmergencyStopped,
            self::Disputed,
            self::Completed => true,
            default => false
        };
    }

    public function isCancellable(): bool
    {
        return match ($this) {
            self::Completed,
            self::PaymentPending,
            self::RiderCancelled,
            self::DriverCancelled,
            self::SystemCancelled,
            self::EmergencyStopped,
            self::Disputed => false,
            default => true
        };
    }
}
