<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessBranchStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_branch_staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('business_branch_id')->unsigned();
            $table->foreign('business_branch_id')->references('id')->on('business_branches')->onDelete('cascade');
            $table->bigInteger('staff_id')->unsigned();
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('manage_reservations')->default(false);
            $table->boolean('manage_waiting_list')->default(false);
            $table->boolean('manage_pickups')->default(false);
            $table->boolean('manage_catering_bookings')->default(false);
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
        Schema::dropIfExists('business_branch_staff');
    }
}
