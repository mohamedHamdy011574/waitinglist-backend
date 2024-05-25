<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('catering_order_id')->unsigned()->index();
            $table->foreign('catering_order_id')->references('id')->on('catering_orders')->onDelete('cascade');
            $table->bigInteger('cat_packg_id')->unsigned()->index();
            $table->foreign('cat_packg_id')->references('id')->on('catering_packages')->onDelete('cascade');
            $table->string('package_title',100)->nullable();
            $table->integer('quantity')->unsigned();
            $table->decimal('unit_price', 8,3)->default(0);
            $table->decimal('addons_price', 8,3)->default(0);
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
        Schema::dropIfExists('catering_order_items');
    }
}
