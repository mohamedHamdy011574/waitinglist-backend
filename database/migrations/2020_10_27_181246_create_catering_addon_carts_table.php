<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringAddonCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_addon_carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('catering_cart_item_id')->unsigned()->index();
            $table->foreign('catering_cart_item_id')->references('id')->on('catering_cart_items')->onDelete('cascade');
            $table->integer('item_count')->unsigned();
            $table->integer('quantity')->unsigned();
            $table->decimal('total', 8,3)->default(0);
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
        Schema::dropIfExists('catering_addon_carts');
    }
}
