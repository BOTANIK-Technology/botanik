<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chat_id')->nullable();
            $table->bigInteger('yclients_id')->nullable();
            $table->bigInteger('beauty_id')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('username', 32)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50);
            $table->integer('age')->nullable();
            $table->boolean('sex')->nullable();
            $table->integer('spent_bonus')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('bonus')->nullable();
            $table->integer('frequency')->nullable();
            $table->float('spent_money')->nullable();

            $table->unsignedBigInteger('last_service')->nullable();
            $table->unsignedBigInteger('favorite_service')->nullable();
//            $table->foreign('last_service')->references('id')->on('services')->onDelete('cascade');
//            $table->foreign('favorite_service')->references('id')->on('services')->onDelete('cascade');

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
        Schema::dropIfExists('telegram_users');
    }
}
