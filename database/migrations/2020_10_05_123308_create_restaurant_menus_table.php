<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurant_menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->bigInteger('menu_category_id')->unsigned()->nullable();
            $table->foreign('menu_category_id')->references('id')->on('menu_categories')->onDelete('cascade');
            $table->text('menu_item_photo')->nullable();
            $table->decimal('price',8,3)->default(0.00);
            $table->string('currency')->nullable();
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('restaurant_menu_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->bigInteger('restaurant_menu_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->string('locale')->index();
            $table->unique(['restaurant_menu_id', 'locale']);
            $table->unique(['name', 'locale']);
            $table->foreign('restaurant_menu_id')->references('id')->on('restaurant_menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_menu_translations');
        Schema::dropIfExists('restaurant_menus');
    }
}
