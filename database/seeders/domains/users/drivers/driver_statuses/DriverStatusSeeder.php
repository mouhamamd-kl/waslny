<?php

namespace Database\Seeders\domains\users\drivers\driver_statuses;

use App\Models\DriverStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $driverStatuses = [
            [
                'name' => 'Offline',
            ],
            [
                'name' => 'Online',
            ],
            [
                'name' => 'OnTrip',
            ],

        ];
        foreach ($driverStatuses as $driverStatus) {

            DriverStatus::updateOrCreate(
                [
                    'name' => $driverStatus['name'],
                ]
            );
        }
    }
}
