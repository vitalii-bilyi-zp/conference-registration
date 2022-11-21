<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            $categoryId = null;

            if ($i % 5 === 0) {
                $categoryId = Category::inRandomOrder()->first()->id;
            }

            Category::factory()->create([
                'category_id' => $categoryId,
            ]);
        }
    }
}
