<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('given_by');
            $table->unsignedBigInteger('blog_id');
            $table->longText('review');
            $table->enum('status',['active','inactive'])->default('active');            
            $table->timestamps();
            
            $table->foreign('given_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('blog_id')->references('id')->on('food_blogs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
