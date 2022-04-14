<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('text');

            $table->integer('age_start')->nullable();
            $table->integer('age_end')->nullable();
            $table->boolean('sex')->nullable();
            $table->integer('frequency')->nullable();
            $table->text('img')->nullable();
            $table->longText('button')->nullable();

            $table->unsignedBigInteger('last_service')->nullable();
            $table->unsignedBigInteger('favorite_service')->nullable();
            $table->foreign('last_service')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('favorite_service')->references('id')->on('services')->onDelete('cascade');

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
        Schema::dropIfExists('mails');
    }
}
