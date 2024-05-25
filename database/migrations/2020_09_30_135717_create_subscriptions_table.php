<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->bigInteger('vendor_id')->unsigned()->nullable();
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('sub_package_id')->unsigned()->nullable();
            $table->foreign('sub_package_id')->references('id')->on('subscription_packages')->onDelete('set null');
            
            $table->boolean('for_restaurant')->default(false);
            $table->boolean('for_catering')->default(false);
            $table->boolean('for_food_truck')->default(false);
            $table->boolean('reservation')->default(false);
            $table->boolean('waiting_list')->default(false);
            $table->boolean('pickup')->default(false);
            $table->integer('branches_include')->default(1);
            $table->integer('subscription_period')->default(1);
            
            $table->decimal('package_price',8,3)->default(0.00);
            $table->string('currency')->default('KD');
            $table->date('purchase_date');
            $table->date('package_end_date');
            $table->enum('status',['active','inactive'])->default('active');
            $table->enum('payment_mode',['online','manual'])->default('online');
            $table->enum('payment_status',['unpaid','paid'])->default('unpaid');
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
        Schema::dropIfExists('subscriptions');
    }
}
