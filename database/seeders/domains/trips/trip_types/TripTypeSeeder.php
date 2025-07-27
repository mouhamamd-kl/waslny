<?php

namespace Database\Seeders\domains\trips\trip_types;

use App\Enums\TripTypeEnum;
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
        foreach (TripTypeEnum::cases() as $tripType) {
            TripType::updateOrCreate(
                ['name' => $tripType->value],
                [
                    'description' => $tripType->description(),
                    'is_active' => true
                ]
            );
        }

        // Optional: Add inactive trip types if needed
        TripType::updateOrCreate(
            ['name' => 'Airport Transfer'],
            [
                'description' => 'Specialized service to/from airports',
                'is_active' => false
            ]
        );
    }
}
