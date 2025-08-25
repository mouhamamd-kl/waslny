<?php

namespace Tests\Feature;

use App\Enums\LocationTypeEnum;
use App\Models\Driver;
use App\Models\Rider;
use App\Models\Trip;
use App\Enums\TripStatusEnum;
use App\Models\TripStatus;
use App\Services\DriverService;
use App\Services\RiderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TripLifecycleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Rider $rider;
    protected Driver $driver;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->seed();

        $this->rider = app(RiderService::class)->search_first();
        $this->driver = app(DriverService::class)->search_first();
    }

    /** @test */
    public function rider_can_request_a_trip_successfully()
    {
        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $response = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('trips', [
            'rider_id' => $this->rider->id,
        ]);
    }

    /** @test */
    public function driver_can_accept_a_trip_request()
    {
        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $tripResponse = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);
        $tripId = $tripResponse->json('data.id');

        $trip = Trip::find($tripId);
        $trip->transitionTo(TripStatusEnum::Searching);

        $response = $this->actingAs($this->driver, 'driver-api')->postJson("/api/trips/driver/{$tripId}/accept");

        $response->assertStatus(200);
        $this->assertDatabaseHas('trips', [
            'id' => $tripId,
            'driver_id' => $this->driver->id,
            'trip_status_id' => TripStatus::where('name', TripStatusEnum::DriverAssigned->value)->first()->id,
        ]);
    }

    /** @test */
    public function driver_can_arrive_at_pickup_location()
    {
        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $tripResponse = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);
        $tripId = $tripResponse->json('data.id');

        $trip = Trip::find($tripId);
        $trip->assignDriver($this->driver);
        $trip->transitionTo(TripStatusEnum::DriverEnRoute);

        $response = $this->actingAs($this->driver, 'driver-api')->postJson("/api/trips/driver/{$tripId}/arrive");

        $response->assertStatus(200);
        $this->assertDatabaseHas('trips', [
            'id' => $tripId,
            'trip_status_id' => TripStatus::where('name', TripStatusEnum::DriverArrived->value)->first()->id,
        ]);
    }

    /** @test */
    public function driver_can_start_the_trip()
    {
        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $tripResponse = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);
        $tripId = $tripResponse->json('data.id');

        $trip = Trip::find($tripId);
        $trip->assignDriver($this->driver);
        $trip->transitionTo(TripStatusEnum::DriverArrived);

        $response = $this->actingAs($this->driver, 'driver-api')->postJson("/api/trips/driver/{$tripId}/start");

        $response->assertStatus(200);
        $this->assertDatabaseHas('trips', [
            'id' => $tripId,
            'trip_status_id' => TripStatus::where('name', TripStatusEnum::OnGoing->value)->first()->id,
        ]);
    }

    /** @test */
    public function driver_can_complete_the_trip()
    {
        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $tripResponse = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);
        $tripId = $tripResponse->json('data.id');

        $trip = Trip::find($tripId);
        $trip->assignDriver($this->driver);
        $trip->transitionTo(TripStatusEnum::OnGoing);

        $response = $this->actingAs($this->driver, 'driver-api')->postJson("/api/trips/driver/{$tripId}/complete");

        $response->assertStatus(200);
        $this->assertDatabaseHas('trips', [
            'id' => $tripId,
            'trip_status_id' => TripStatus::where('name', TripStatusEnum::Completed->value)->first()->id,
        ]);
    }

    /** @test */
    public function rider_can_cancel_a_trip()
    {
        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $tripResponse = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);
        $tripId = $tripResponse->json('data.id');

        $response = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/cancel', ['trip_id' => $tripId]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trips', [
            'id' => $tripId,
            'trip_status_id' => TripStatus::where('name', TripStatusEnum::RiderCancelled->value)->first()->id,
        ]);
    }

    /** @test */
    public function driver_can_cancel_a_trip()
    {
        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $tripResponse = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);
        $tripId = $tripResponse->json('data.id');

        $trip = Trip::find($tripId);
        $trip->assignDriver($this->driver);
        $trip->transitionTo(TripStatusEnum::DriverEnRoute);

        $response = $this->actingAs($this->driver, 'driver-api')->postJson("/api/trips/driver/cancel", ['trip_id' => $tripId]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trips', [
            'id' => $tripId,
            'trip_status_id' => TripStatus::where('name', TripStatusEnum::DriverCancelled->value)->first()->id,
        ]);
    }

    /** @test */
    public function system_can_cancel_a_trip()
    {
        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $tripResponse = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);
        $tripId = $tripResponse->json('data.id');

        $trip = Trip::find($tripId);
        $trip->transitionTo(TripStatusEnum::SystemCancelled);

        $this->assertDatabaseHas('trips', [
            'id' => $tripId,
            'trip_status_id' => TripStatus::where('name', TripStatusEnum::SystemCancelled->value)->first()->id,
        ]);
    }

    /** @test */
    public function it_prevents_invalid_state_transitions()
    {
        $this->expectException(\App\Exceptions\InvalidTransitionException::class);

        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $tripResponse = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);
        $tripId = $tripResponse->json('data.id');

        $trip = Trip::find($tripId);
        $trip->assignDriver($this->driver);
        $trip->transitionTo(TripStatusEnum::Completed);

        $trip->transitionTo(TripStatusEnum::OnGoing);
    }

    /** @test */
    public function rider_cannot_cancel_a_system_cancelled_trip()
    {
        $tripData = [
            'trip_type_id' => 1,
            'trip_time_type_id' => 1,
            'car_service_level_id' => 1,
            'payment_method_id' => 1,
            'locations' => [
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 1,
                    'location_type' => LocationTypeEnum::Pickup->value,
                ],
                [
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$this->faker->longitude, $this->faker->latitude],
                    ],
                    'location_order' => 2,
                    'location_type' => LocationTypeEnum::DropOff->value,
                ],
            ],
        ];

        $tripResponse = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/request', $tripData);
        $tripId = $tripResponse->json('data.id');

        $trip = Trip::find($tripId);
        $trip->transitionTo(TripStatusEnum::SystemCancelled);

        $response = $this->actingAs($this->rider, 'rider-api')->postJson('/api/trips/rider/cancel', ['trip_id' => $tripId]);

        $response->assertStatus(422); // Unprocessable Entity
    }
}
