<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemConfig::updateOrCreate(['key' => 'driver_share'], ['value' => '0.8']);
        SystemConfig::updateOrCreate(['key' => 'driver_cancel_fee'], ['value' => '10']);
        SystemConfig::updateOrCreate(['key' => 'rider_cancel_fee'], ['value' => '10']);
    }
}
