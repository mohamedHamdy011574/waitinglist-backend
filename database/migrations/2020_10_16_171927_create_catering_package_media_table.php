<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringPackageMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_package_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('catering_pkg_id')->unsigned();
            $table->foreign('catering_pkg_id')->references('id')->on('catering_packages')->onDelete('cascade');
            $table->text('image');
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
        Schema::dropIfExists('catering_package_media');
    }
}
