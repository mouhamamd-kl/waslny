<?php

namespace App\Enums\channels;

enum BroadCastChannelEnum: string
{
    case RIDER = 'riders.{riderId}';
    case DRIVER = 'drivers.{driverId}';
    case DRIVERS_ONLINE = 'online-drivers';
    case TRIP = 'trips.{tripId}';

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
