<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHowToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('how_to', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('display_order');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('how_to_translations', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('how_to_id')->unsigned();
            $table->longText('question')->nullable();
            $table->longText('answer')->nullable();
            $table->string('locale')->index();
            $table->unique(['how_to_id', 'locale']);
            $table->foreign('how_to_id')->references('id')->on('how_to')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('how_to_translations');
        Schema::dropIfExists('how_to');
    }
}
