<?php

namespace Database\Seeders\domains\trips\trip_statuses;

use App\Enums\TripStatusEnum;
use App\Models\TripStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (TripStatusEnum::values() as $status) {
            TripStatus::updateOrCreate(
                ['name' => $status]
            );
        }
    }
}
