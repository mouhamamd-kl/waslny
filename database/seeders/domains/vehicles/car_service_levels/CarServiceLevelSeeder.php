<?php

namespace Database\Seeders\domains\vehicles\car_service_levels;
// database/seeders/CarServiceLevelSeeder.php
use App\Models\CarServiceLevel;
use Illuminate\Database\Seeder;

class CarServiceLevelSeeder extends Seeder
{
    public function run()
    {
        $levels = [
            ['name' => 'Classic'],
            ['name' => 'Comfort'],
            ['name' => 'V.I.P'],
            ['name' => 'SUV']
        ];

        foreach ($levels as $level) {
            CarServiceLevel::create($level);
        }
    }
}
