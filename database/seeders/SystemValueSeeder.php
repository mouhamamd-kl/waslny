<?php

namespace Database\Seeders;

use App\Enums\DriverStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\TripStatusEnum;
use App\Enums\TripTimeTypeEnum;
use App\Enums\TripTypeEnum;
use App\Models\DriverStatus;
use App\Models\PaymentMethod;
use App\Models\TripStatus;
use App\Models\TripTimeType;
use App\Models\TripType;
use Illuminate\Database\Seeder;

class SystemValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (TripStatusEnum::cases() as $case) {
            TripStatus::where('name', $case->value)->update(['system_value' => $case->value]);
        }

        foreach (TripTimeTypeEnum::cases() as $case) {
            TripTimeType::where('name', $case->value)->update(['system_value' => $case->value]);
        }

        foreach (TripTypeEnum::cases() as $case) {
            TripType::where('name', $case->value)->update(['system_value' => $case->value]);
        }

        foreach (PaymentMethodEnum::cases() as $case) {
            PaymentMethod::where('name', $case->value)->update(['system_value' => $case->value]);
        }

        foreach (DriverStatusEnum::cases() as $case) {
            DriverStatus::where('name', $case->value)->update(['system_value' => $case->value]);
        }
    }
}
