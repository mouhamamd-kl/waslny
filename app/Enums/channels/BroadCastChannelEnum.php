<?php

namespace App\Enums\channels;

enum BroadCastChannelEnum: string
{
    case RIDER = 'rider.{riderId}';
    case DRIVER = 'driver.{driverId}';
    case DRIVERS_ONLINE = 'online-drivers';
    case TRIP = 'trip.{tripId}';

    public function pattern(): string
    {
        return $this->value;
    }

    public function bind(array $parameters): string
    {
        return str_replace(
            array_map(fn($k) => '{' . $k . '}', array_keys($parameters)),
            array_values($parameters),
            $this->value
        );
    }
}
