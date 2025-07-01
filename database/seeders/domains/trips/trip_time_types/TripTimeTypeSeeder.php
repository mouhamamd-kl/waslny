<?php

namespace Database\Seeders\domains\trips\trip_time_types;

use App\Models\TripTimeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripTimeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tripTimeTypes = [
            [
                'name' => 'Instant',
            ],
            [
                'name' => 'Scheduled',
            ],
        ];
        foreach ($tripTimeTypes as $tripTimeType) {

            TripTimeType::updateOrCreate(
                [
                    'name' => $tripTimeType['name'],
                ]
            );
        }
    }
}
