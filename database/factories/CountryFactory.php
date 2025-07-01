<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $carProducingCountries = [
            'Japan',
            'United States',
            'South Korea',
            'China',
            'Italy',
            'France',
            'United Kingdom',
            'Sweden',
            'Czech Republic',
            'Spain',
            'Mexico',
            'Canada',
            'Brazil'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($carProducingCountries),
            'is_active' => $this->faker->boolean(90), // 90% chance active
        ];
    }

    public function active(): static
    {
        return $this->state(fn() => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}
