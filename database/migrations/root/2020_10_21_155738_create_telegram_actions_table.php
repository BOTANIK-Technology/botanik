<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_actions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('t_user_id');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('user_name', 32)->nullable();
            $table->string('lang', 2)->nullable();

            $table->unsignedBigInteger('button_id');
            $table->foreign('button_id')->references('id')->on('tg_buttons')->onDelete('cascade');

            $table->text('text')->nullable();
            $table->integer('voice')->nullable();
            $table->integer('audio')->nullable();
            $table->integer('photo')->nullable();
            $table->integer('video')->nullable();
            $table->integer('video_note')->nullable();
            $table->integer('document')->nullable();
            $table->integer('location')->nullable();
            $table->string('phone', 50)->nullable();

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
        Schema::dropIfExists('telegram_actions');
    }
}
