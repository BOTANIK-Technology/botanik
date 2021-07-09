<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('yclients_id')->nullable();
            $table->string('beauty_id', 255)->nullable();
            $table->unsignedBigInteger('type_service_id');
            $table->foreign('type_service_id')->references('id')->on('type_services')->onDelete('cascade');
            $table->string('name')->default('name')->unique();
            $table->float('price');
            $table->integer('bonus')->nullable();

            $table->unsignedBigInteger('interval_id')->nullable();
            $table->foreign('interval_id')->references('id')->on('intervals')->onDelete('cascade');
            $table->integer('range')->default(0);

            $table->boolean('cash_pay')->default(0);
            $table->boolean('bonus_pay')->default(0);
            $table->boolean('online_pay')->default(0);

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
        Schema::dropIfExists('services');
    }
}
