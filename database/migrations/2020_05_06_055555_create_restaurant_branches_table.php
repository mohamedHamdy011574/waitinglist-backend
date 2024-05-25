<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rest_branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('restaurant_id')->unsigned();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('is_master')->default(false);
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();

            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
        });

        Schema::create('rest_branch_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('rest_branch_id')->unsigned();
            $table->string('name');
            $table->text('address');
            $table->string('locale')->index();
            $table->unique(['rest_branch_id', 'locale']);
            $table->foreign('rest_branch_id')->references('id')->on('rest_branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rest_branch_translations');
        Schema::dropIfExists('restaurant_branches');
    }
}
