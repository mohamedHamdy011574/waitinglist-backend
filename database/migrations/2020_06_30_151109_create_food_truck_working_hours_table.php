<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTruckWorkingHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_truck_working_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('food_truck_id')->unsigned();
            $table->foreign('food_truck_id')->references('id')->on('food_trucks')->onDelete('cascade');
            $table->unsignedTinyInteger('from_day');
            $table->unsignedTinyInteger('to_day');
            $table->time('from_time');
            $table->time('to_time');
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
        Schema::dropIfExists('food_truck_working_hours');
    }
}
