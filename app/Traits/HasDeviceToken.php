<?php

namespace App\Traits;

trait HasDeviceToken
{
    public function routeNotificationForFirebase()
    {
        return $this->device_token;
    }
}
