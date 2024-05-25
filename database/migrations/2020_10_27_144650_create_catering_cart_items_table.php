<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_cart_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('catering_cart_id')->unsigned()->index();
            $table->foreign('catering_cart_id')->references('id')->on('catering_carts')->onDelete('cascade');
            $table->bigInteger('cat_packg_id')->unsigned()->index();
            $table->foreign('cat_packg_id')->references('id')->on('catering_packages')->onDelete('cascade');
            $table->string('package_title',100)->nullable();
            $table->integer('quantity')->unsigned();
            $table->decimal('unit_price', 8,3)->default(0);
            $table->decimal('addons_price', 8,3)->default(0);
            $table->text('special_request')->nullable();
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
        Schema::dropIfExists('catering_cart_items');
    }
}
