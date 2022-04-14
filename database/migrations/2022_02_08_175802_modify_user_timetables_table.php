<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUserTimetablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::dropIfExists('user_timetables');
        Schema::dropIfExists('users_addresses');
        Schema::dropIfExists('users_services');

        Schema::create('users_timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_slots_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->string('month');
            $table->json('schedule');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users_timetables');
        Schema::enableForeignKeyConstraints();
    }
}
