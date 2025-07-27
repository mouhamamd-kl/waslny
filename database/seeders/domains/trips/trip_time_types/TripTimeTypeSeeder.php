<?php

namespace Database\Seeders\domains\trips\trip_time_types;

use App\Enums\TripTimeTypeEnum;
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
        foreach (TripTimeTypeEnum::cases() as $type) {
            TripTimeType::updateOrCreate(
                ['name' => $type->value],
                [
                    'description' => $type->description(),
                    'is_active' => true
                ]
            );
        }
    }
}
