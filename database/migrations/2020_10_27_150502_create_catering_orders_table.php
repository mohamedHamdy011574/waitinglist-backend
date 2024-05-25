<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_orders', function (Blueprint $table) {
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
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->integer('item_count')->unsigned();
            $table->integer('quantity')->unsigned();
            $table->decimal('sub_total', 8,3)->default(0);
            $table->decimal('addons_total', 8,3)->default(0);
            $table->decimal('discount', 8,3)->default(0);
            $table->decimal('taxes', 8,3)->default(0);
            $table->decimal('grand_total', 8,3)->default(0);
            $table->datetime('order_date');
            $table->date('due_date');
            $table->time('due_time');
            $table->time('end_time');
            $table->enum('order_status',['pending', 'booked', 'cancelled', 'completed'])->default('pending');
            $table->enum('payment_status',['pending','failed', 'paid', 'unpaid'])->default('pending');
            $table->string('payment_mode')->nullable();
            $table->datetime('rate_notification_at')->nullable();
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
        Schema::dropIfExists('catering_orders');
    }
}
