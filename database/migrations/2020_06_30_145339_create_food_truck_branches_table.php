<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTruckBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftruck_branches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('food_truck_id')->unsigned();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('is_master')->default(false);
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();

            $table->foreign('food_truck_id')->references('id')->on('food_trucks')->onDelete('cascade');
        });

        Schema::create('ftruck_branch_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('ftruck_branch_id')->unsigned();
            $table->string('name');
            $table->text('address');
            $table->string('locale')->index();
            $table->unique(['ftruck_branch_id', 'locale']);
            $table->foreign('ftruck_branch_id')->references('id')->on('ftruck_branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ftruck_branch_translations');
        Schema::dropIfExists('ftruck_branches');
    }
}
