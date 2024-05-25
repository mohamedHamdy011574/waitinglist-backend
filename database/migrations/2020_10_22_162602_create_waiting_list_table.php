<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaitingListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waiting_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('first_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->integer('token_number')->nullable();
            $table->bigInteger('business_id')->unsigned()->nullable();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('set null');
            $table->bigInteger('business_branch_id')->unsigned()->nullable();
            $table->foreign('business_branch_id')->references('id')->on('business_branches')->onDelete('set null');
            $table->integer('reserved_chairs');
            $table->datetime('wl_datetime');
            $table->date('wl_date');
            $table->time('wl_time');
            $table->enum('status',['in_queue', 'cancelled', 'checked_in', 'checked_out'])->default('in_queue');
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
        Schema::dropIfExists('waiting_list');
    }
}
