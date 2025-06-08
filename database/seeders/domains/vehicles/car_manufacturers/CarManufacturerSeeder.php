<?php

namespace Database\Seeders\domains\vehicles\car_manufacturers;

use App\Models\CarManufacturer;
use App\Models\Country;
use Database\Seeders\domains\vehicles\countries\CountrySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    private array $majorManufacturers = [
        'Germany' => [
            'BMW' => true,
            'Mercedes-Benz' => true,
            'Volkswagen' => true,
            'Audi' => true,
            'Porsche' => true,
        ],
        'Japan' => [
            'Toyota' => true,
            'Honda' => true,
            'Nissan' => true,
            'Mazda' => true,
            'Subaru' => true,
        ],
        'United States' => [
            'Ford' => true,
            'Tesla' => true,
            'Chevrolet' => true,
        ],
        'South Korea' => [
            'Hyundai' => true,
            'Kia' => true,
        ],
    ];

    public function run(): void
    {
        // Ensure countries exist first
        $this->call([CountrySeeder::class]);

        // Seed major manufacturers
        foreach ($this->majorManufacturers as $countryName => $manufacturers) {
            $country = Country::where('name', $countryName)->first();

            foreach ($manufacturers as $name => $isActive) {
                CarManufacturer::updateOrCreate(
                    ['name' => $name],
                    [
                        'country_id' => $country->id,
                        'is_active' => $isActive,
                    ]
                );
            }
        }

        // Create additional random manufacturers
        CarManufacturer::factory()
            ->count(15)
            ->create();
    }
}
