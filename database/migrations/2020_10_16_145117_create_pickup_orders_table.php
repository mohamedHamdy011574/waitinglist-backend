<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('first_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->bigInteger('business_branch_id')->unsigned()->nullable();
            $table->foreign('business_branch_id')->references('id')->on('business_branches')->onDelete('cascade');
            $table->bigInteger('coupon_id')->unsigned()->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->integer('item_count')->unsigned();
            $table->integer('quantity')->unsigned();
            $table->decimal('sub_total', 8,3)->default(0);
            $table->decimal('discount', 8,3)->default(0);
            $table->decimal('taxes', 8,3)->default(0);
            $table->decimal('grand_total', 8,3)->default(0);
            $table->datetime('pickup_date');
            $table->date('due_date');
            $table->time('due_time');
            $table->time('end_time');
            $table->enum('order_status',['received','confirmed', 'ready_for_pickup', 'picked_up', 'cancelled'])->default('received');
            $table->enum('payment_status',['pending','failed', 'paid', 'unpaid'])->default('pending');
            $table->string('payment_mode')->nullable();
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
        Schema::dropIfExists('pickup_orders');
    }
}
