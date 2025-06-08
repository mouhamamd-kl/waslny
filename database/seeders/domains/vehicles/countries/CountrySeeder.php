<?php

namespace Database\Seeders\domains\vehicles\countries;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private array $majorCarManufacturingCountries = [
        ['name' => 'Germany', 'is_active' => true],     // BMW, Mercedes, Volkswagen
        ['name' => 'Japan', 'is_active' => true],       // Toyota, Honda, Nissan
        ['name' => 'United States', 'is_active' => true], // Ford, GM, Tesla
        ['name' => 'South Korea', 'is_active' => true], // Hyundai, Kia
        ['name' => 'China', 'is_active' => true],       // Geely, BYD
        ['name' => 'Italy', 'is_active' => true],       // Ferrari, Lamborghini
        ['name' => 'France', 'is_active' => true],      // Renault, Peugeot
        ['name' => 'United Kingdom', 'is_active' => true], // Jaguar, Land Rover
        ['name' => 'Sweden', 'is_active' => true],      // Volvo
        ['name' => 'Czech Republic', 'is_active' => true], // Skoda
    ];

    public function run(): void
    {
        // Seed major car manufacturing countries
        foreach ($this->majorCarManufacturingCountries as $country) {
            Country::updateOrCreate(
                ['name' => $country['name']],
                $country
            );
        }

        // Add some random inactive countries for testing
        Country::factory()
            ->count(3)
            ->inactive()
            ->create();
    }
}
