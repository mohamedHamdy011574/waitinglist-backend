<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_blogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('added_by');
            $table->string('recipe_name');
            $table->longText('description');
            $table->string('recipe_video')->nullable();
            $table->string('recipe_audio')->nullable();
            $table->timestamps();

            $table->foreign('added_by')->references('id')->on('bloggers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('food_blogs');
    }
}
