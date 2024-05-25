<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTruckCuisineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_truck_cuisine', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('food_truck_id')->unsigned();
            $table->bigInteger('cuisine_id')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('food_truck_id')->references('id')->on('food_trucks')->onDelete('cascade');
            $table->foreign('cuisine_id')->references('id')->on('cuisines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('food_truck_cuisine');
    }
}
