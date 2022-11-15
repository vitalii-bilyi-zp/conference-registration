<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conference_user', function (Blueprint $table) {
            $table->foreign('conference_id')
                ->references('id')
                ->on('conferences')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conference_user', function (Blueprint $table) {
            $table->dropForeign(['conference_id']);
            $table->dropForeign(['user_id']);
        });
    }
};
