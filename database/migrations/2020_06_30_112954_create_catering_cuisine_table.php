<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringCuisineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_cuisine', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('catering_id')->unsigned();
            $table->bigInteger('cuisine_id')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('catering_id')->references('id')->on('catering')->onDelete('cascade');
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
        Schema::dropIfExists('catering_cuisine');
    }
}
