<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringPlanAddonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_plan_addon', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('catering_plan_id')->unsigned();
            $table->foreign('catering_plan_id')->references('id')->on('catering_plans')->onDelete('cascade');
            $table->bigInteger('catering_addon_id')->unsigned();
            $table->foreign('catering_addon_id')->references('id')->on('catering_addons')->onDelete('cascade');
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
        Schema::dropIfExists('catering_plan_addon');
    }
}
