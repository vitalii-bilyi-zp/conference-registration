<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Country;
use App\Models\Category;

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
        $categoryId = null;

        if (rand(0, 1)) {
            $categoryId = Category::inRandomOrder()->first()->id;
        }

        return [
            'title' => 'Conference ' . fake()->word(),
            'date' => date_format(fake()->dateTimeBetween('now', '+1 years'), 'Y-m-d'),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'country_id' => Country::inRandomOrder()->first()->id,
            'category_id' => $categoryId,
        ];
    }
}
