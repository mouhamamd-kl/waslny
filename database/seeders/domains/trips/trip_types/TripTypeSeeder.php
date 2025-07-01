<?php

namespace Database\Seeders\domains\trips\trip_types;

use App\Models\TripType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tripTypes = [
            [
                'name' => 'Regular',
            ],
            [
                'name' => 'Different Locations',
            ],
            [
                'name' => 'Round Trip',
            ],
        ];
        foreach ($tripTypes as $tripType) {
            TripType::updateOrCreate(
                [
                    'name' => $tripType['name'],
                ]
            );
        }
    }
}
