<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Provider ' . fake()->company,
            'is_inhouse' => false,
            'is_default' => false,
            'is_active' => true,
            'contact_name' => fake()->name,
            'email' => fake()->unique()->companyEmail,
            'phone' => fake()->phoneNumber,
            'provider_type' => fake()->randomElement(['transport', 'tour', 'water']),
            'priority' => 3,
        ];
    }
}
