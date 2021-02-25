<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bot_name');
            $table->boolean('status')->default(true);

            $table->unsignedBigInteger('package_id');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');

            $table->string('db_name', 50)->unique();
            $table->string('slug', 32)->unique();
            $table->text('img')->nullable();
            $table->text('token');
            $table->text('pay_token')->nullable();
            $table->boolean('catalog')->default(false);

            $table->unsignedBigInteger('owner_id');
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');

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
        Schema::dropIfExists('businesses');
    }
}
