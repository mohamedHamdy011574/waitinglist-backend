<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('first_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->bigInteger('business_id')->unsigned()->nullable();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('set null');
            $table->bigInteger('business_branch_id')->unsigned()->nullable();
            $table->foreign('business_branch_id')->references('id')->on('business_branches')->onDelete('set null');
            $table->bigInteger('coupon_id')->unsigned()->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
            $table->integer('reserved_chairs');
            $table->decimal('total',8,3)->default(0);
            $table->decimal('tax',8,3)->default(0);
            $table->decimal('discount',8,3)->default(0);
            $table->datetime('check_in_date');
            $table->date('due_date');
            $table->time('due_time');
            $table->time('end_time');
            $table->enum('status',['pending','confirmed', 'cancelled', 'checked_in', 'checked_out'])->default('pending');
            $table->enum('payment_status',['pending','paid', 'unpaiid'])->default('pending');
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
        Schema::dropIfExists('reservations');
    }
}
