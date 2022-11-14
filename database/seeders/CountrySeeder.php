<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/countries.json');
        $countries = json_decode($json);

        foreach ($countries as $value) {
            Country::updateOrCreate(
                ['iso2' => $value->iso2],
                ['name' => $value->name, 'dial_code' => $value->dialCode, 'phone_mask' => $value->phoneMask],
            );
        }
    }
}
