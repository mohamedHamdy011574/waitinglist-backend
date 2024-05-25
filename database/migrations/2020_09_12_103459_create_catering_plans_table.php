<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->integer('persons_served_min')->nullable();
            $table->integer('persons_served_max')->nullable();
            $table->boolean('sunday_serving')->default(false);
            $table->boolean('monday_serving')->default(false);
            $table->boolean('tuesday_serving')->default(false);
            $table->boolean('wednesday_serving')->default(false);
            $table->boolean('thursday_serving')->default(false);
            $table->boolean('friday_serving')->default(false);
            $table->boolean('saturday_serving')->default(false);
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->decimal('plan_rate',8,3)->default(0.00);
            $table->string('currency')->default('KD');
            $table->boolean('served_in_restaurant')->default(false);
            $table->boolean('served_off_premises')->default(false);
            $table->integer('setup_time')->nullable();
            $table->string('setup_time_unit')->nullable();
            $table->integer('max_time')->nullable();
            $table->string('max_time_unit')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('catering_plan_translations', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('catering_plan_id')->unsigned();
            $table->string('plan_name')->nullable();
            $table->longText('description')->nullable();
            $table->longText('food_serving')->nullable();
            $table->string('locale')->index();
            $table->unique(['catering_plan_id', 'locale']);
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
        Schema::dropIfExists('catering_plan_translations');
        Schema::dropIfExists('catering_plans');
    }
}
