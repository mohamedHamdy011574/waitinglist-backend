<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('pickup_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->time('from_time');
            $table->time('to_time');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('pickup_hour_translations', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('pkp_hour_id')->unsigned();
            $table->string('shift_name')->nullable();
            $table->string('locale')->index();
            $table->unique(['pkp_hour_id', 'locale']);
            $table->unique(['shift_name', 'locale']);
            $table->foreign('pkp_hour_id')->references('id')->on('pickup_hours')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pickup_hours');
        Schema::dropIfExists('pickup_hour_translations');
    }
}
