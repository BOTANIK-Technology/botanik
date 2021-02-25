<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telegram_user_id');
            $table->foreign('telegram_user_id')->references('id')->on('telegram_users')->onDelete('cascade');
            $table->integer('type')->nullable();
            $table->integer('service')->nullable();
            $table->integer('address')->nullable();
            $table->integer('master')->nullable();
            $table->integer('record')->nullable();
            $table->integer('stars')->nullable();
            $table->longText('data')->nullable();
            $table->date('date')->nullable();
            $table->string('time', 10)->nullable();

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
        Schema::dropIfExists('client_sessions');
    }
}
