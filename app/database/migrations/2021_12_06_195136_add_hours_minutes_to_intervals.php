<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoursMinutesToIntervals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('intervals', function (Blueprint $table) {
            //
            $table->integer('hoursField')->default(0);
            $table->integer('minutesField')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('intervals', function (Blueprint $table) {
            //
            $table->dropColumn('hoursField');
            $table->dropColumn('minutesField');
        });
    }
}
