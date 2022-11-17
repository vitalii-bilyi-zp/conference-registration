<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dayStart = Carbon::create(0, 1, 1, 9, 0, 0)->format('H:i:s');
        $dayEnd = Carbon::create(0, 1, 1, 18, 0, 0)->format('H:i:s');

        Schema::table('conferences', function (Blueprint $table) use ($dayStart, $dayEnd) {
            $table->time('day_start')->default($dayStart)->after('country_id');
            $table->time('day_end')->default($dayEnd)->after('day_start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn('day_start');
            $table->dropColumn('day_end');
        });
    }
};
