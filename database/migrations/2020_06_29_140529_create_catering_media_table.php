<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('catering_id')->unsigned();
            $table->enum('media_type', ['menu', 'banner'])->default('banner');
            $table->text('media_path');
            $table->timestamps();
            $table->foreign('catering_id')->references('id')->on('catering')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catering_media');
    }
}
