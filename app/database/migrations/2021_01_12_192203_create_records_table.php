<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('yclients_id')->nullable();
            $table->string('beauty_id', 255)->nullable();
            $table->unsignedBigInteger('telegram_user_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('address_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign('telegram_user_id')->references('id')->on('telegram_users')->onDelete('cascade');
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->dateTime('transfer')->nullable();
            $table->boolean('status')->default(true);

            $table->date('date');
            $table->text('time');

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
        Schema::dropIfExists('records');
    }
}
