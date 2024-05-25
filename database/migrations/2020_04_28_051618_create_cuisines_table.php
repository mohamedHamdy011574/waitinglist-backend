<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuisinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuisines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('image')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('cuisine_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('cuisine_id')->unsigned();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->string('locale')->index();
            $table->unique(['cuisine_id', 'locale']);
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
        Schema::dropIfExists('cuisine_translations');
        Schema::dropIfExists('cuisines');
    }
}
