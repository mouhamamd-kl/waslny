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

    public function bind(string $parameter): string
    {
        $pattern = '/\{[^}]+\}/';
        return preg_replace($pattern, $parameter, $this->value);
    }
}
