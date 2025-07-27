<?php
return [
    'created' => ':resource created successfully.',
    'updated' => ':resource updated successfully.',
    'deleted' => ':resource deleted successfully.',
    'not_found' => ':resource not found.',
    'forbidden' => 'You are not authorized to perform this action.',
    'unauthenticated' => 'Please login to continue.',
    'success' => 'Operation completed successfully.',
    'validation_failed' => 'Data validation failed.',

    'rider' => [
        'created' => 'Rider account created successfully',
        'updated' => 'Rider account updated successfully',
        'deleted' => 'Rider account deleted successfully',
        'retrieved' => 'Rider account retrieved successfully',
        'list' => 'Riders list retrieved successfully',
        'completion' => 'Rider profile completed successfully',
        'error' => [
            'profile_already_completed' => 'Profile already completed',
            'profile_incomplete' => 'Please complete your profile to access this feature',
        ],
    ],

    'rider_location_folder' => [
        'created' => 'Rider locations folder created successfully',
        'updated' => 'Rider locations folder updated successfully',
        'deleted' => 'Rider locations folder deleted successfully',
        'retrieved' => 'Rider locations folder retrieved successfully',
        'list' => 'Rider location folders list retrieved successfully',
        'error' => [
            'creation_failed' => 'Failed to create locations folder',
            'update_failed' => 'Failed to update locations folder',
            'delete_failed' => 'Failed to delete locations folder',
            'not_found' => 'Locations folder not found',
        ]
    ],

    'rider_saved_location' => [
        'created' => 'Location saved successfully',
        'updated' => 'Saved location updated successfully',
        'deleted' => 'Saved location deleted successfully',
        'retrieved' => 'Saved location retrieved successfully',
        'list' => 'Saved locations list retrieved successfully',
        'error' => [
            'creation_failed' => 'Failed to save location',
            'update_failed' => 'Failed to update saved location',
            'delete_failed' => 'Failed to delete saved location',
            'not_found' => 'Saved location not found',
            'limit_reached' => 'Maximum saved locations limit reached',
            'invalid_alias' => 'Invalid location alias',
            'invalid_coordinates' => 'Invalid location coordinates',
            'duplicate_location' => 'Location already exists',
            'sharing_failed' => 'Failed to share location',
            'privacy_restriction' => 'Location sharing not permitted',
            'geofence_conflict' => 'Location outside permitted area',
            'address_resolve_failed' => 'Failed to resolve location address',
            'type_conflict' => 'Location type already exists (home/work)',
            'favorite_limit' => 'Maximum favorite locations limit reached'
        ]
    ],

    'country' => [
        'created' => 'Country created successfully',
        'updated' => 'Country updated successfully',
        'deleted' => 'Country deleted successfully',
        'retrieved' => 'Country retrieved successfully',
        'list' => 'Countries list retrieved successfully',
        'error' => [
            'activation_failed' => 'Failed to activate country',
            'deactivation_failed' => 'Failed to deactivate country',
        ]
    ],

    'coupon' => [
        'created' => 'Coupon created successfully',
        'updated' => 'Coupon updated successfully',
        'deleted' => 'Coupon deleted successfully',
        'retrieved' => 'Coupon retrieved successfully',
        'list' => 'Coupons list retrieved successfully',
        'applied' => 'Coupon applied successfully',
        'expired' => 'Coupon has expired',
        'invalid' => 'Invalid coupon',
        'activated' => 'Coupon activated successfully',
        'deactivated' => 'Coupon deactivated successfully',
        'error' => [
            'activation_failed' => 'Failed to activate coupon',
            'deactivation_failed' => 'Failed to deactivate coupon',
        ],
    ],

    'rider_coupon' => [
        'created' => 'Rider coupon created successfully',
        'updated' => 'Rider coupon updated successfully',
        'deleted' => 'Rider coupon deleted successfully',
        'retrieved' => 'Rider coupon retrieved successfully',
        'list' => 'Rider coupons list retrieved successfully',
        'error' => [
            'creation_failed' => 'Failed to create rider coupon',
            'update_failed' => 'Failed to update rider coupon',
            'delete_failed' => 'Failed to delete rider coupon',
            'not_found' => 'Rider coupon not found',
        ]
    ],

    'car_manufacture' => [
        'created' => 'Car manufacturer created successfully',
        'updated' => 'Car manufacturer updated successfully',
        'deleted' => 'Car manufacturer deleted successfully',
        'retrieved' => 'Car manufacturer retrieved successfully',
        'list' => 'Car manufacturers list retrieved successfully',
        'activated' => 'Manufacturer activated successfully',
        'deactivated' => 'Manufacturer deactivated successfully',
        'error' => [
            'activation_failed' => 'Failed to activate manufacturer',
            'deactivation_failed' => 'Failed to deactivate manufacturer',
        ],
    ],

    'car_model' => [
        'created' => 'Car model created successfully',
        'updated' => 'Car model updated successfully',
        'deleted' => 'Car model deleted successfully',
        'retrieved' => 'Car model retrieved successfully',
        'list' => 'Car models list retrieved successfully',
        'activated' => 'Car model activated successfully',
        'deactivated' => 'Car model deactivated successfully',
        'error' => [
            'activation_failed' => 'Failed to activate car model',
            'deactivation_failed' => 'Failed to deactivate car model',
        ]
    ],

    'car_service_level' => [
        'created' => 'Car service level created successfully',
        'updated' => 'Car service level updated successfully',
        'deleted' => 'Car service level deleted successfully',
        'retrieved' => 'Car service level retrieved successfully',
        'list' => 'Car service levels list retrieved successfully',
        'activated' => 'Service level activated successfully',
        'deactivated' => 'Service level deactivated successfully',
        'error' => [
            'activation_failed' => 'Failed to activate service level',
            'deactivation_failed' => 'Failed to deactivate service level',
        ]
    ],

    'payment_method' => [
        'created' => 'Payment method created successfully',
        'updated' => 'Payment method updated successfully',
        'deleted' => 'Payment method deleted successfully',
        'retrieved' => 'Payment method retrieved successfully',
        'list' => 'Payment methods list retrieved successfully',
        'activated' => 'Payment method activated successfully',
        'deactivated' => 'Payment method deactivated successfully',
        'default_set' => 'Default payment method set successfully',
        'error' => [
            'activation_failed' => 'Failed to activate payment method',
            'deactivation_failed' => 'Failed to deactivate payment method',
        ]
    ],

    'driver_status' => [
        'created' => 'Driver status created successfully',
        'updated' => 'Driver status updated successfully',
        'deleted' => 'Driver status deleted successfully',
        'retrieved' => 'Driver status retrieved successfully',
        'list' => 'Driver statuses list retrieved successfully',
        'not_found' => 'Driver status not found',
        'exists' => 'Driver status already exists',
        'in_use' => 'Cannot delete - status is currently in use',
        'status_changed' => 'Driver status changed successfully',
        'default_cannot_delete' => 'Cannot delete default status',
    ],

    'pricing' => [
        'created' => 'Pricing created successfully',
        'updated' => 'Pricing updated successfully',
        'deleted' => 'Pricing deleted successfully',
        'retrieved' => 'Pricing retrieved successfully',
        'list' => 'Pricing list retrieved successfully',
        'activated' => 'Pricing plan activated successfully',
        'deactivated' => 'Pricing plan deactivated successfully',
        'error' => [
            'activation_failed' => 'Failed to activate pricing plan',
            'deactivation_failed' => 'Failed to deactivate pricing plan',
        ]
    ],

    'trip_type' => [
        'created' => 'Trip type created successfully',
        'updated' => 'Trip type updated successfully',
        'deleted' => 'Trip type deleted successfully',
        'retrieved' => 'Trip type retrieved successfully',
        'list' => 'Trip types list retrieved successfully',
        'activated' => 'Trip type activated successfully',
        'deactivated' => 'Trip type deactivated successfully',
        'error' => [
            'creation_failed' => 'Failed to create trip type',
            'update_failed' => 'Failed to update trip type',
            'delete_failed' => 'Failed to delete trip type',
            'not_found' => 'Trip type not found',
            'activation_failed' => 'Failed to activate trip type',
            'deactivation_failed' => 'Failed to deactivate trip type',
        ]
    ],

    'trip_time_type' => [
        'created' => 'Trip time type created successfully',
        'updated' => 'Trip time type updated successfully',
        'deleted' => 'Trip time type deleted successfully',
        'retrieved' => 'Trip time type retrieved successfully',
        'list' => 'Trip time types list retrieved successfully',
        'error' => [
            'creation_failed' => 'Failed to create trip time type',
            'update_failed' => 'Failed to update trip time type',
            'delete_failed' => 'Failed to delete trip time type',
            'not_found' => 'Trip time type not found',
            'activation_failed' => 'Failed to activate trip time type',
            'deactivation_failed' => 'Failed to deactivate trip time type',
        ]
    ],

    'trip' => [
        'created' => 'Trip created successfully',
        'updated' => 'Trip updated successfully',
        'deleted' => 'Trip deleted successfully',
        'retrieved' => 'Trip retrieved successfully',
        'list' => 'Trips list retrieved successfully',
        'started' => 'Trip started successfully',
        'completed' => 'Trip completed successfully',
        'canceled' => 'Trip canceled successfully',
        'driver_assigned' => 'Driver assigned to trip successfully',
        'driver_arrived' => 'Driver arrived at pickup point',
        'rider_picked_up' => 'Rider picked up successfully',
        'route_updated' => 'Trip route updated successfully',
        'payment_processed' => 'Payment processed successfully',
        'rated' => 'Trip rated successfully',
        'scheduled' => 'Trip scheduled successfully',
        'error' => [
            'creation_failed' => 'Failed to create trip',
            'update_failed' => 'Failed to update trip',
            'delete_failed' => 'Failed to delete trip',
            'not_found' => 'Trip not found',
            'start_failed' => 'Failed to start trip',
            'completion_failed' => 'Failed to complete trip',
            'cancellation_failed' => 'Failed to cancel trip',
            'invalid_status' => 'Invalid trip status',
            'driver_unavailable' => 'No driver available',
            'driver_assignment_failed' => 'Failed to assign driver',
            'rider_not_ready' => 'Rider not ready for pickup',
            'pickup_failed' => 'Pickup failed',
            'route_calculation_failed' => 'Route calculation failed',
            'payment_failed' => 'Payment processing failed',
            'rating_failed' => 'Failed to rate trip',
            'scheduling_failed' => 'Failed to schedule trip',
            'timeout' => 'Waiting time expired',
            'distance_exceeded' => 'Distance exceeded estimated value',
            'price_mismatch' => 'Final price doesn\'t match estimate',
            'vehicle_mismatch' => 'Vehicle type mismatch',
            'rider_cancellation_fee' => 'Trip cancellation fee applied',
            'driver_cancellation_penalty' => 'Driver cancellation penalty applied',
            'no_drivers_available' => 'No drivers available within the search radius. Please try again.',
        ]
    ],

    'trip_location' => [
        'created' => 'Trip location created successfully',
        'updated' => 'Trip location updated successfully',
        'deleted' => 'Trip location deleted successfully',
        'retrieved' => 'Trip location retrieved successfully',
        'list' => 'Trip locations list retrieved successfully',
        'completed' => 'Location arrival confirmed successfully',
        'sequence_updated' => 'Locations sequence updated successfully',
        'eta_updated' => 'Estimated arrival time updated successfully',
        'arrival_confirmed' => 'Actual arrival time recorded successfully',
        'route_optimized' => 'Locations route optimized successfully',
        'error' => [
            'creation_failed' => 'Failed to create trip location',
            'update_failed' => 'Failed to update trip location',
            'delete_failed' => 'Failed to delete trip location',
            'not_found' => 'Trip location not found',
            'completion_failed' => 'Failed to confirm location arrival',
            'invalid_sequence' => 'Invalid locations sequence',
            'eta_calculation_failed' => 'Failed to calculate estimated arrival time',
            'arrival_confirmation_failed' => 'Failed to record actual arrival time',
            'route_optimization_failed' => 'Failed to optimize locations route',
            'invalid_location_type' => 'Invalid location type',
            'order_conflict' => 'Location order conflict',
            'trip_completed' => 'Cannot modify completed trip',
            'invalid_coordinates' => 'Invalid location coordinates',
            'distance_calculation_failed' => 'Failed to calculate distance between locations',
            'sequence_out_of_bounds' => 'Location order out of allowed range',
            'already_completed' => 'Location already completed',
            'dependency_exists' => 'Cannot delete - dependent locations exist',
        ]
    ],

    'trip_status' => [
        'created' => 'Trip status created successfully',
        'updated' => 'Trip status updated successfully',
        'deleted' => 'Trip status deleted successfully',
        'retrieved' => 'Trip status retrieved successfully',
        'list' => 'Trip statuses list retrieved successfully',
        'error' => [
            'creation_failed' => 'Failed to create trip status',
            'update_failed' => 'Failed to update trip status',
            'delete_failed' => 'Failed to delete trip status',
            'not_found' => 'Trip status not found',
        ]
    ],

    'driver' => [
        'created' => 'Driver account created successfully',
        'updated' => 'Driver account updated successfully',
        'deleted' => 'Driver account deleted successfully',
        'retrieved' => 'Driver account retrieved successfully',
        'list' => 'Drivers list retrieved successfully',
        'completion' => 'Driver profile completed successfully',
        'suspended' => 'Driver account suspended successfully',
        'reinstate' => 'Driver account reinstated successfully',
        'error' => [
            'suspension_failed' => 'Failed to suspend account',
            'reinstate_failed' => 'Failed to reinstate account',
        ],
        'car' => [
            'created' => 'Vehicle created successfully'
        ]
    ],

    'auth' => [
        'verification' => [
            'required' => 'Email not verified. A new verification link has been sent. Please check your email.',
            'resent' => 'A new verification link has been sent to your email.',
            'sent' => 'Verification code sent'
        ],
        'registered' => 'Registration successful. Please check your email for verification.',
        'logout' => 'Logged out successfully',
        'profile' => [
            'retrieved' => 'Profile data retrieved successfully',
            'updated' => 'Profile updated successfully',
        ],
        'error' => [
            'update_failed' => 'Failed to update profile',
        ]
    ],

    'error' => [
        'generic' => 'An error occurred. Please try again later',
        'creation_failed' => 'Creation failed',
        'update_failed' => 'Update failed',
        'delete_failed' => 'Deletion failed',
    ],
];
