<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatingAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seating_area', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('seating_area_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('stg_area_id')->unsigned();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->string('locale')->index();
            $table->unique(['stg_area_id', 'locale']);
            $table->foreign('stg_area_id')->references('id')->on('seating_area')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seating_area_translations');
        Schema::dropIfExists('seating_area');
    }
}
