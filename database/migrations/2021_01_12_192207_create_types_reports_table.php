<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('total')->nullable();
            $table->integer('records')->nullable();
            $table->integer('feeds')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('type_service_id');
            $table->unsignedBigInteger('report_id');
            $table->foreign('type_service_id')->references('id')->on('type_services')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('types_reports');
    }
}
