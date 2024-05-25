<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('link');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('restaurant_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('restaurant_id')->unsigned();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('locale')->index();
            $table->unique(['restaurant_id', 'locale']);
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_translations');
        Schema::dropIfExists('restaurants');
    }
}
