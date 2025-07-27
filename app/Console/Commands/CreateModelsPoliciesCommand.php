<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateModelsPoliciesCommand extends Command
{
    protected $signature = 'make:polcies';
    protected $description = 'Create models Policies';

    public function handle(): int
    {

        return $this->createCustomAdmin();
    }



    protected function createCustomAdmin(): int
    {
        $this->info('Creating models policies...');

        $models = [
            "CarManufacturer",
            "CarModel",
            "CarServiceLevel",
            "Country",
            "Coupon",
            "Driver",
            "DriverCar",
            "DriverStatus",
            "PaymentMethod",
            "Pricing",
            "Rider",
            "RiderCoupon",
            "RiderFolder",
            "RiderSavedLocation",
            "Trip",
            "TripLocation",
            "TripStatus",
            "TripTimeType",
            "TripType",
        ];
        try {
            foreach ($models as $model) {
                $this->call('make:policy', [
                    'name' => "{$model}Policy",
                    '--model' => "App\\Models\\{$model}",
                ]);
            }
            return 0;
        } catch (\Exception $e) {
            $this->error('Error creating admin: ' . $e->getMessage());
            return 1;
        }
    }
}
