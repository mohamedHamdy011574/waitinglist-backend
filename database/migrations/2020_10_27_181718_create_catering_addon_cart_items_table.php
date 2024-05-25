<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringAddonCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_addon_cart_items', function (Blueprint $table) 
        {
            $table->bigIncrements('id');
            $table->bigInteger('catering_addon_cart_id')->unsigned()->index();
            $table->foreign('catering_addon_cart_id')->references('id')->on('catering_addon_carts')->onDelete('cascade');
            $table->bigInteger('cat_addon_id')->unsigned()->index();
            $table->foreign('cat_addon_id')->references('id')->on('catering_addons')->onDelete('cascade');
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
        Schema::dropIfExists('catering_addon_cart_items');
    }
}
