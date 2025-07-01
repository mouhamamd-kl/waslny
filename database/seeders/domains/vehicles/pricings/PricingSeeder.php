<?php

namespace Database\Seeders\domains\vehicles\pricings;

use App\Models\CarServiceLevel;
use App\Models\Pricing;
use Database\Seeders\CarServiceLevelSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create base pricing for each service level
        $pricings = [
            [
                'serviceLevel' => 'Classic',
                'price' => 10000,
                'isActive' => true
            ],
            [
                'serviceLevel' => 'Comfort',
                'price' => 15000,
                'isActive' => true
            ],
            [
                'serviceLevel' => 'V.I.P',
                'price' => 20000,
                'isActive' => true
            ],
            [
                'serviceLevel' => 'SUV',
                'price' => 30000,
                'isActive' => true
            ],
        ];

        foreach ($pricings as $pricing) {
            $serviceLevel = CarServiceLevel::where('name', $pricing['serviceLevel'])->first();

            if ($serviceLevel) {
                Pricing::updateOrCreate(
                    ['car_service_level_id' => $serviceLevel->id],
                    [
                        'price_per_km' => $pricing['price'],
                        'is_active' => $pricing['isActive'],
                    ]
                );
            }
        }
    }
}
