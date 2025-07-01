<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\domains\coupons\CouponSeeder;
use Database\Seeders\domains\payments\payment_methods\PaymentMethodSeeder;
use Database\Seeders\domains\trips\trip_statuses\TripStatusSeeder;
use Database\Seeders\domains\trips\trip_time_types\TripTimeTypeSeeder;
use Database\Seeders\domains\trips\trip_types\TripTypeSeeder;
use Database\Seeders\domains\users\drivers\driver_statuses\DriverStatusSeeder;
use Database\Seeders\domains\vehicles\car_manufacturers\CarManufacturerSeeder;
use Database\Seeders\domains\vehicles\car_models\CarModelSeeder;
use Database\Seeders\domains\vehicles\car_service_levels\CarServiceLevelSeeder;
use Database\Seeders\domains\vehicles\countries\CountrySeeder;
use Database\Seeders\domains\vehicles\pricings\PricingSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            CountrySeeder::class,
            CarManufacturerSeeder::class,
            CarServiceLevelSeeder::class,
            CarModelSeeder::class,
            PricingSeeder::class,

            CouponSeeder::class,

            PaymentMethodSeeder::class,

            DriverStatusSeeder::class,

            TripStatusSeeder::class,
            TripTimeTypeSeeder::class,
            TripTypeSeeder::class,
        ]);
    }
}
