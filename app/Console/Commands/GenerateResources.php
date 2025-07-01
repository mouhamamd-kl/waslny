<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateResources extends Command
{
    protected $signature = 'make:generate-resources';
    protected $description = 'Create all resources for the app';

    public function handle()
    {
        $models = array(
            'Rider',
            'RiderCoupon',
            'RiderFolder',
            'RiderSavedLocation',
            'Admin',
            'Driver',
            'DriverCar',
            'DriverStatus',
            'CarManufacturer',
            'CarModel',
            'CarServiceLevel',
            'Country',
            'Pricing',
            'Coupon',
            'PaymentMethod',
            'Trip',
            'TripLocation',
            'TripStatus',
            'TripTimeType',
            'TripType'
        );
        foreach ($models as $model) {
            $this->call('make:resource', [
                'name' => $model . 'Resource',
            ]);
        }


        $this->info('Operation completed successfully!');
    }
}
