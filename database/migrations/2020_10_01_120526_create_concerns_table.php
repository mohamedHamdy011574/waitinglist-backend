<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConcernsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concerns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('concern_translations', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('concern_id')->unsigned();
            $table->string('concern')->nullable();
            $table->string('locale')->index();
            $table->unique(['concern_id', 'locale']);
            $table->unique(['concern', 'locale']);
            $table->foreign('concern_id')->references('id')->on('concerns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('concerns');
    }
}
