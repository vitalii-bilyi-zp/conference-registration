<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Conference;
use App\Models\Lecture;
use App\Models\User;

use Faker\Factory as Faker;
use Carbon\Carbon;

class ConferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        Conference::factory()
            ->count(10)
            ->create()
            ->each(function ($conference) use (&$faker) {
                $user = User::inRandomOrder()
                    ->where('type', '=', User::ANNOUNCER_TYPE)
                    ->first();

                if (!isset($user)) {
                    return;
                }

                $conferenceDate = Carbon::parse($conference->date);
                $conferenceDate->setHour(9);
                $conferenceStart = $conferenceDate->format('Y-m-d H:i:s');
                $lectureDuration = rand(Lecture::MIN_DURATION, Lecture::MAX_DURATION);
                $lectureEnd = $conferenceDate->addMinutes($lectureDuration)->format('Y-m-d H:i:s');

                Lecture::create([
                    'title' => 'Lecture ' . $faker->word(),
                    'description' => $faker->text(100),
                    'lecture_start' => $conferenceStart,
                    'lecture_end' => $lectureEnd,
                    'conference_id' => $conference->id,
                    'user_id' => $user->id,
                    'category_id' => $conference->category_id,
                ]);

                $conference->users()->attach($user->id);
            });
    }
}
