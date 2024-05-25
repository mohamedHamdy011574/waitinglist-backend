<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTruckMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_truck_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('food_truck_id')->unsigned();
            $table->enum('media_type', ['menu', 'banner'])->default('banner');
            $table->text('media_path');
            $table->timestamps();
            $table->foreign('food_truck_id')->references('id')->on('food_trucks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('food_truck_media');
    }
}
