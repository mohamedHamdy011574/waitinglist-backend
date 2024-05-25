<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTrucksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_trucks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('manager_id')->nullable()->unsigned();
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            $table->text('link');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('food_truck_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('food_truck_id')->unsigned();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('locale')->index();
            $table->unique(['food_truck_id', 'locale']);
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
        Schema::dropIfExists('food_truck_translations');
        Schema::dropIfExists('food_trucks');
    }
}
