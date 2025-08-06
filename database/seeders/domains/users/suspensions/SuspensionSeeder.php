<?php

namespace Database\Seeders\domains\users\suspensions;

use App\Enums\SuspensionReasonEnum;
use App\Models\Suspension;
use Illuminate\Database\Seeder;

class SuspensionSeeder extends Seeder
{
    /**
     *      $table->string('reason');
            $table->string('admin-msg');
            $table->string('user-msg');
            $table->boolean('is_active')->default(true);
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (SuspensionReasonEnum::cases() as $reason) {
            Suspension::updateOrCreate(
                // Match records by 'code'
                ['code' => $reason->value],
                // Update/create these attributes
                [
                    'reason' => $reason->value,
                    'admin_msg' => $reason->adminMessage(),
                    'user_msg' => $reason->message(),
                    'is_system_defined' => true,
                ]
            );
        }
    }
}
