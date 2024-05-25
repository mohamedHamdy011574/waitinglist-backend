<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickupOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pickup_order_id')->unsigned()->index();
            $table->foreign('pickup_order_id')->references('id')->on('pickup_orders')->onDelete('cascade');
            $table->bigInteger('menu_id')->unsigned()->index();
            $table->foreign('menu_id')->references('id')->on('restaurant_menus')->onDelete('cascade');
            $table->string('menu_title',100)->nullable();
            $table->integer('quantity')->unsigned();
            $table->decimal('unit_price', 8,3)->default(0);
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
        Schema::dropIfExists('pickup_order_items');
    }
}
