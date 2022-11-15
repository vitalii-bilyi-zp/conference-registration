<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Conference;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@groupbwt.com'],
            [
                'firstname' => 'Admin',
                'type' => User::ADMIN_TYPE,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(10),
            ]
        );

        for ($i = 1; $i <= 5; $i++) {
            $user = User::factory()->create([
                'email' => "user+$i@groupbwt.com",
            ]);

            $conference = Conference::inRandomOrder()->first();
            $conference->users()->attach($user->id);
        }
    }
}
