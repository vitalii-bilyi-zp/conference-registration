<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Conference;

class ConferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Conference::factory()->count(10)->create();
    }
}
