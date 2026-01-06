<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Zone>
 */
class ZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Zone ' . fake()->unique()->word,
            'priority' => fake()->numberBetween(1, 10),
            'transfer_time_minutes' => fake()->numberBetween(15, 120),
            'color' => fake()->hexColor,
            'service_type' => fake()->randomElement(['transfer', 'tour', 'all']),
            'coordinates' => json_decode('[[{"lat":20.6274,"lng":-87.0799},{"lat":20.629,"lng":-87.085},{"lat":20.625,"lng":-87.09}]]', true),
            'provider_id' => Provider::factory(),
        ];
    }
}
