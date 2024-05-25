<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('price',8,3);
            $table->text('link');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('catering_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('catering_id')->unsigned();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('locale')->index();
            $table->unique(['catering_id', 'locale']);
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
        Schema::dropIfExists('catering_translations');
        Schema::dropIfExists('catering');
    }
}
