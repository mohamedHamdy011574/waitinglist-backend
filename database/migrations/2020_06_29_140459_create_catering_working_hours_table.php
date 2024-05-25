<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringWorkingHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_working_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('catering_id')->unsigned();
            $table->foreign('catering_id')->references('id')->on('catering')->onDelete('cascade');
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
        Schema::dropIfExists('catering_working_hours');
    }
}
