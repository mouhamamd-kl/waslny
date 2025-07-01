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
    public function run()
    {
        $countries = [
            ['name' => 'South Korea'],
            ['name' => 'Japan'],
            ['name' => 'Germany'],
            ['name' => 'United States'],
            ['name' => 'France'],
            ['name' => 'Sweden'],
            ['name' => 'China'],
            ['name' => 'Malaysia'],
            ['name' => 'Romania'],
            ['name' => 'Unknown'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
