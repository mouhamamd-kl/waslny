<?php

namespace Database\Seeders\domains\coupons;

use App\Models\Coupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 random coupons
        Coupon::factory()
            ->count(50)
            ->create();

        // Create 10 expired coupons
        Coupon::factory()
            ->count(10)
            ->expired()
            ->create();

        // Create 5 upcoming coupons
        Coupon::factory()
            ->count(5)
            ->upcoming()
            ->create();

        // Create specific test coupons
      
    }
}
