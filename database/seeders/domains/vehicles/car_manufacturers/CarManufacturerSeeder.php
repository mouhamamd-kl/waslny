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
    private $countryMapping = [
        'Kia' => 'South Korea',
        'Hyundai' => 'South Korea',
        'Honda' => 'Japan',
        'Mazda' => 'Japan',
        'Mitsubishi' => 'Japan',
        'Chevrolet' => 'United States',
        'Pejout' => 'France', // Note: Correct spelling is Peugeot
        'Toyota' => 'Japan',
        'Skoda' => 'Germany',
        'Saba' => 'Sweden', // Note: Likely Saab
        'BYD' => 'China',
        'Proton' => 'Malaysia',
        'Dacia' => 'Romania',
        'Renult' => 'France', // Renault
        'Ford' => 'United States',
        'Nissan' => 'Japan',
        'Volgsvagen' => 'Germany', // Volkswagen
        'Audi' => 'Germany',
    ];

    public function run()
    {
        foreach ($this->countryMapping as $brand => $countryName) {
            $country = Country::where('name', $countryName)->first();

            CarManufacturer::create([
                'name' => $brand,
                'country_id' => $country->id,
                'is_active' => true
            ]);
        }
    }
}
