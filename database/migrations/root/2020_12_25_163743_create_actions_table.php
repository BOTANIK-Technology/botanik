<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');

            $table->unsignedBigInteger('ip_address_id')->nullable();
            $table->foreign('ip_address_id')->references('id')->on('ip_addresses')->onDelete('cascade');

            $table->unsignedBigInteger('user_agent_id')->nullable();
            $table->foreign('user_agent_id')->references('id')->on('user_agents')->onDelete('cascade');

            $table->unsignedBigInteger('action_name_id')->nullable();
            $table->foreign('action_name_id')->references('id')->on('action_names')->onDelete('cascade');

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
        Schema::dropIfExists('actions');
    }
}
