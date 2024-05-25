<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantTimingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_timings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('rest_branch_id')->unsigned();
            $table->unsignedTinyInteger('week_day');
            $table->time('from');
            $table->time('to');
            $table->integer('reservations_capacity');
            $table->timestamps();

            $table->foreign('rest_branch_id')->references('id')->on('rest_branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_timings');
    }
}
