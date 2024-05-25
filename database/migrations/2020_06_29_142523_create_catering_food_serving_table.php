<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringFoodServingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_food_serving', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });

        Schema::create('catering_fs_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('catering_fs_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['catering_fs_id', 'locale']);
            $table->foreign('catering_fs_id')->references('id')->on('catering_food_serving')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catering_fs_translations');
        Schema::dropIfExists('catering_food_serving');
    }
}
