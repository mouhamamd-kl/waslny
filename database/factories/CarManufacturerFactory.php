<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarManufacturer>
 */
class CarManufacturerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $manufacturersByCountry = [
            'Germany' => ['BMW', 'Mercedes-Benz', 'Audi', 'Volkswagen', 'Porsche'],
            'Japan' => ['Toyota', 'Honda', 'Nissan', 'Mazda', 'Subaru'],
            'United States' => ['Ford', 'Chevrolet', 'Tesla', 'Dodge', 'Jeep'],
            'South Korea' => ['Hyundai', 'Kia'],
            'Italy' => ['Ferrari', 'Lamborghini', 'Fiat', 'Alfa Romeo'],
            'France' => ['Renault', 'Peugeot', 'Citroën'],
            'United Kingdom' => ['Jaguar', 'Land Rover', 'Mini', 'Aston Martin'],
            'Sweden' => ['Volvo', 'Koenigsegg'],
            'Czech Republic' => ['Škoda'],
            'China' => ['BYD', 'Geely', 'NIO']
        ];

        // Get a random country with manufacturers
        $country = Country::whereIn('name', array_keys($manufacturersByCountry))
            ->inRandomOrder()
            ->first() ?? Country::factory()->create();

        return [
            'name' => $this->faker->unique()->randomElement(
                $manufacturersByCountry[$country->name] ?? ['Unknown']
            ),
            'country_id' => $country->id,
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
