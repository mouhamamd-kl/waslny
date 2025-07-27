<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Coupon::class; // <-- Add this line
    public function definition(): array
    {
        $max_uses = $this->faker->numberBetween(10, 1000);
        $used_count = $this->faker->numberBetween(0, $max_uses - 1);
        return [
            'code' => Str::upper(Str::random(10)),
            'max_uses' => $max_uses,
            'used_count' => $used_count,
            'percent' => $this->faker->randomFloat(2, 0.5, 20), // Generates: e.g., 5.75, 12.30, 0.50
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
        ];
    }

    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'end_date' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
            ];
        });
    }

    public function upcoming(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'start_date' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            ];
        });
    }
}
