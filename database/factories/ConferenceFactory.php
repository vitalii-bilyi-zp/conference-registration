<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conference>
 */
class ConferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->word(),
            'date' => fake()->date('Y-m-d'),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'country_id' => Country::inRandomOrder()->first()->id,
        ];
    }
}
