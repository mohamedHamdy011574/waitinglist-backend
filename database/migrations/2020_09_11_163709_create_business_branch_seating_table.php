<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessBranchSeatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_branch_seating', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('business_branch_id')->unsigned();
            $table->bigInteger('stg_area_id')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('business_branch_id')->references('id')->on('business_branches')->onDelete('cascade');
            $table->foreign('stg_area_id')->references('id')->on('seating_area')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_branch_seating');
    }
}
