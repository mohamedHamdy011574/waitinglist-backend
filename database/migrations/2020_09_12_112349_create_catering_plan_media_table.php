<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringPlanMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_plan_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('catering_plan_id')->unsigned();
            $table->text('media_type')->nullable();
            $table->text('media_path')->nullable();
            $table->timestamps();
            $table->foreign('catering_plan_id')->references('id')->on('catering_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catering_plan_media');
    }
}
